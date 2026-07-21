import os
import json
import time
import uuid
import logging
from typing import List, Optional, Dict, Any
from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel, Field
from config.settings import settings
from utils.security import verify_api_token
from services.provider_manager import AIProviderManager
from schemas.gateway_schema import make_gateway_response
from exceptions.gateway_exceptions import AIException

logger = logging.getLogger("ai_caption_generator")
router = APIRouter(prefix="/ai/captions", dependencies=[Depends(verify_api_token)])

class CaptionGenerateRequest(BaseModel):
    brand_intelligence: Dict[str, Any] = Field(..., description="Brand Intelligence JSON")
    marketing_strategy: Dict[str, Any] = Field(..., description="Marketing Strategy JSON")
    content_calendar: Dict[str, Any] = Field(..., description="Content Calendar Entry JSON")
    tone: Optional[str] = Field(None, description="Preferred tone override")
    language: Optional[str] = Field(None, description="Preferred language override")

def load_prompt_template() -> str:
    path = os.path.join(os.path.dirname(os.path.dirname(__file__)), "prompts", "caption_generator.txt")
    if not os.path.exists(path):
        raise FileNotFoundError(f"Caption generator prompt template not found at {path}")
    with open(path, "r", encoding="utf-8") as f:
        return f.read()

@router.post("/generate")
async def generate_caption(req: CaptionGenerateRequest):
    start_time = time.perf_counter()
    request_id = uuid.uuid4().hex
    active_provider_name = settings.DEFAULT_AI_PROVIDER
    
    logger.info(
        f"[ReqId: {request_id}] Generating caption. Provider: {active_provider_name}"
    )
    
    try:
        template = load_prompt_template()
        
        # Serialize fields into readable formatted lines for prompt injection
        brand_intel_str = json.dumps(req.brand_intelligence, indent=2)
        marketing_strategy_str = json.dumps(req.marketing_strategy, indent=2)
        
        cc = req.content_calendar
        title = cc.get("title", "N/A")
        topic = cc.get("topic", "N/A")
        platform = cc.get("platform", "N/A")
        content_pillar = cc.get("content_pillar", "N/A")
        campaign_name = cc.get("campaign_name", "N/A")
        goal = cc.get("goal", "N/A")
        content_type = cc.get("content_type", "N/A")
        post_format = cc.get("post_format", "N/A")
        priority = cc.get("priority", "Medium")
        notes = cc.get("notes", "N/A")
        
        tone = req.tone or req.brand_intelligence.get("brand_tone") or "Professional"
        # Support array style languages
        languages = req.brand_intelligence.get("languages", ["English"])
        language = req.language or (languages[0] if languages and isinstance(languages, list) else "English")

        # Format the prompt
        prompt = template.format(
            brand_intelligence=brand_intel_str,
            marketing_strategy=marketing_strategy_str,
            title=title,
            topic=topic,
            platform=platform,
            content_pillar=content_pillar,
            campaign_name=campaign_name,
            goal=goal,
            content_type=content_type,
            post_format=post_format,
            priority=priority,
            notes=notes,
            tone=tone,
            language=language
        )
        
        provider = AIProviderManager.get_provider(active_provider_name)
        result = await provider.generate_text(prompt, timeout=settings.AI_TIMEOUT)
        
        text_response = result.get("text", "").strip()
        
        # Clean up markdown formatting if returned
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
                f"[ReqId: {request_id}] JSON decode error. Raw response text: {text_response}. Error: {str(jde)}"
            )
            raise HTTPException(
                status_code=status.HTTP_502_BAD_GATEWAY,
                detail=f"AI Provider returned invalid JSON structure: {str(jde)}"
            )
            
        duration = time.perf_counter() - start_time
        logger.info(
            f"[ReqId: {request_id}] Caption generation success. Provider: {active_provider_name}, Model: {result.get('model')}, ExecTime: {duration:.4f}s"
        )
        
        return make_gateway_response(
            success=True,
            message="Caption generated successfully.",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data={
                "caption": parsed_data,
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
            f"[ReqId: {request_id}] Gateway error during caption generation. Provider: {active_provider_name}, Error: {aie.detail}"
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
            f"[ReqId: {request_id}] Unexpected error during caption generation. Error: {str(e)}"
        )
        return make_gateway_response(
            success=False,
            message=f"Unexpected generation error: {str(e)}",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data=None
        )
