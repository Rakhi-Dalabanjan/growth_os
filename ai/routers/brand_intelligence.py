import os
import json
import time
import uuid
import logging
from typing import List, Optional
from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel, Field
from config.settings import settings
from utils.security import verify_api_token
from services.provider_manager import AIProviderManager
from schemas.gateway_schema import make_gateway_response
from exceptions.gateway_exceptions import AIException

logger = logging.getLogger("ai_brand_intelligence")
router = APIRouter(prefix="/ai/brand", dependencies=[Depends(verify_api_token)])

class BrandAnalyzeRequest(BaseModel):
    brand_name: str = Field(..., description="Brand name")
    business_description: Optional[str] = Field(None, description="Business description")
    mission: Optional[str] = Field(None, description="Mission statement")
    vision: Optional[str] = Field(None, description="Vision statement")
    target_audience: Optional[str] = Field(None, description="Target audience description")
    primary_market: Optional[str] = Field(None, description="Primary market")
    brand_tone: Optional[str] = Field(None, description="Preferred tone")
    languages: Optional[List[str]] = Field(None, description="List of brand languages")
    preferred_words: Optional[List[str]] = Field(None, description="List of preferred words")
    restricted_words: Optional[List[str]] = Field(None, description="List of restricted words")
    competitors: Optional[List[str]] = Field(None, description="List of competitor names")
    cta: Optional[str] = Field(None, description="Call to action")
    brand_colors: Optional[List[str]] = Field(None, description="Brand colors list")
    brand_fonts: Optional[List[str]] = Field(None, description="Brand fonts list")

# Load template once or per request. Per request ensures updates are picked up without restarting.
def load_prompt_template() -> str:
    path = os.path.join(os.path.dirname(os.path.dirname(__file__)), "prompts", "brand_analysis.txt")
    if not os.path.exists(path):
        raise FileNotFoundError(f"Prompt template not found at {path}")
    with open(path, "r", encoding="utf-8") as f:
        return f.read()

@router.post("/analyze")
async def analyze_brand(req: BrandAnalyzeRequest):
    start_time = time.perf_counter()
    request_id = uuid.uuid4().hex
    active_provider_name = settings.DEFAULT_AI_PROVIDER
    
    logger.info(
        f"[ReqId: {request_id}] Analyzing brand '{req.brand_name}'. Provider: {active_provider_name}"
    )
    
    try:
        # Load prompt template
        template = load_prompt_template()
        
        # Format list inputs into strings for prompt insertion
        langs_str = ", ".join(req.languages) if req.languages else "N/A"
        pref_words_str = ", ".join(req.preferred_words) if req.preferred_words else "N/A"
        restr_words_str = ", ".join(req.restricted_words) if req.restricted_words else "N/A"
        comps_str = ", ".join(req.competitors) if req.competitors else "N/A"
        colors_str = ", ".join(req.brand_colors) if req.brand_colors else "N/A"
        fonts_str = ", ".join(req.brand_fonts) if req.brand_fonts else "N/A"
        
        # Build prompt
        prompt = template.format(
            brand_name=req.brand_name,
            business_description=req.business_description or "N/A",
            mission=req.mission or "N/A",
            vision=req.vision or "N/A",
            target_audience=req.target_audience or "N/A",
            primary_market=req.primary_market or "N/A",
            brand_tone=req.brand_tone or "N/A",
            languages=langs_str,
            preferred_words=pref_words_str,
            restricted_words=restr_words_str,
            competitors=comps_str,
            cta=req.cta or "N/A",
            brand_colors=colors_str,
            brand_fonts=fonts_str
        )
        
        provider = AIProviderManager.get_provider(active_provider_name)
        result = await provider.generate_text(prompt, timeout=settings.AI_TIMEOUT)
        
        # Parse output JSON
        text_response = result.get("text", "").strip()
        
        # Helper clean up of Markdown block quotes
        if text_response.startswith("```json"):
            text_response = text_response[7:]
        elif text_response.startswith("```"):
            text_response = text_response[3:]
        if text_response.endswith("```"):
            text_response = text_response[:-3]
        text_response = text_response.strip()
        
        try:
            parsed_data = json.loads(text_response)
        except json.JSONDecodeError as jde:
            logger.error(
                f"[ReqId: {request_id}] JSON parse failure. Raw Text: {text_response}. Error: {str(jde)}"
            )
            raise HTTPException(
                status_code=status.HTTP_502_BAD_GATEWAY,
                detail=f"AI provider returned invalid JSON format: {str(jde)}"
            )
            
        duration = time.perf_counter() - start_time
        logger.info(
            f"[ReqId: {request_id}] Brand analysis success. Provider: {active_provider_name}, Model: {result.get('model')}, ExecTime: {duration:.4f}s"
        )
        
        # Include provider and model metadata in return response if needed
        # We wrap the parsed JSON data inside GatewayResponse data
        return make_gateway_response(
            success=True,
            message="Brand intelligence analysis completed successfully.",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data={
                "intelligence": parsed_data,
                "model": result.get("model", settings.DEFAULT_MODELS.get(active_provider_name))
            }
        )
        
    except FileNotFoundError as fnfe:
        duration = time.perf_counter() - start_time
        logger.error(f"[ReqId: {request_id}] Config error: {str(fnfe)}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Prompt template configuration error: {str(fnfe)}"
        )
    except AIException as aie:
        duration = time.perf_counter() - start_time
        logger.error(
            f"[ReqId: {request_id}] Gateway error during analysis. Provider: {active_provider_name}, Error: {aie.detail}"
        )
        return make_gateway_response(
            success=False,
            message=aie.detail,
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data=None
        )
    except HTTPException:
        raise
    except Exception as e:
        duration = time.perf_counter() - start_time
        logger.error(
            f"[ReqId: {request_id}] Unexpected error during brand analysis. Error: {str(e)}"
        )
        return make_gateway_response(
            success=False,
            message=f"Unexpected analysis error: {str(e)}",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data=None
        )
