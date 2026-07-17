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

logger = logging.getLogger("ai_marketing_strategy")
router = APIRouter(prefix="/ai/strategy", dependencies=[Depends(verify_api_token)])

class StrategyGenerateRequest(BaseModel):
    summary: str = Field(..., description="Brand intelligence executive summary")
    brand_personality: Optional[List[str]] = Field(None, description="Personality traits")
    brand_voice: Optional[List[str]] = Field(None, description="Tone & voice rules")
    ideal_customer: Optional[Dict[str, Any]] = Field(None, description="Ideal customer details")
    customer_problems: Optional[List[str]] = Field(None, description="Customer pain points")
    customer_goals: Optional[List[str]] = Field(None, description="Customer goals")
    marketing_objectives: Optional[List[str]] = Field(None, description="Marketing objectives")
    competitor_summary: Optional[str] = Field(None, description="Competitor positioning")
    recommended_content_pillars: Optional[List[str]] = Field(None, description="Content pillars")
    recommended_posting_frequency: Optional[str] = Field(None, description="Suggested posting frequency")
    recommended_cta: Optional[List[str]] = Field(None, description="Recommended CTAs")
    recommended_hashtags: Optional[List[str]] = Field(None, description="Suggested hashtags")
    strengths: Optional[List[str]] = Field(None, description="Strengths list")
    weaknesses: Optional[List[str]] = Field(None, description="Weaknesses list")
    opportunities: Optional[List[str]] = Field(None, description="Opportunities list")
    risks: Optional[List[str]] = Field(None, description="Risks list")

def load_prompt_template() -> str:
    path = os.path.join(os.path.dirname(os.path.dirname(__file__)), "prompts", "strategy_generation.txt")
    if not os.path.exists(path):
        raise FileNotFoundError(f"Strategy prompt template not found at {path}")
    with open(path, "r", encoding="utf-8") as f:
        return f.read()

@router.post("/generate")
async def generate_strategy(req: StrategyGenerateRequest):
    start_time = time.perf_counter()
    request_id = uuid.uuid4().hex
    active_provider_name = settings.DEFAULT_AI_PROVIDER
    
    logger.info(
        f"[ReqId: {request_id}] Generating strategy. Provider: {active_provider_name}"
    )
    
    try:
        template = load_prompt_template()
        
        # Serialize fields into readable formatted lines for prompt injection
        personality_str = ", ".join(req.brand_personality) if req.brand_personality else "N/A"
        voice_str = ", ".join(req.brand_voice) if req.brand_voice else "N/A"
        
        cust_profile_str = "N/A"
        if req.ideal_customer:
            cust_profile_str = "; ".join([f"{k.capitalize()}: {v}" for k, v in req.ideal_customer.items()])
            
        probs_str = ", ".join(req.customer_problems) if req.customer_problems else "N/A"
        goals_str = ", ".join(req.customer_goals) if req.customer_goals else "N/A"
        objectives_str = ", ".join(req.marketing_objectives) if req.marketing_objectives else "N/A"
        pillars_str = ", ".join(req.recommended_content_pillars) if req.recommended_content_pillars else "N/A"
        ctas_str = ", ".join(req.recommended_cta) if req.recommended_cta else "N/A"
        tags_str = ", ".join(req.recommended_hashtags) if req.recommended_hashtags else "N/A"
        
        strengths_str = ", ".join(req.strengths) if req.strengths else "N/A"
        weaknesses_str = ", ".join(req.weaknesses) if req.weaknesses else "N/A"
        opps_str = ", ".join(req.opportunities) if req.opportunities else "N/A"
        risks_str = ", ".join(req.risks) if req.risks else "N/A"
        
        # Format the prompt
        prompt = template.format(
            summary=req.summary,
            brand_personality=personality_str,
            brand_voice=voice_str,
            ideal_customer=cust_profile_str,
            customer_problems=probs_str,
            customer_goals=goals_str,
            marketing_objectives=objectives_str,
            competitor_summary=req.competitor_summary or "N/A",
            recommended_content_pillars=pillars_str,
            recommended_posting_frequency=req.recommended_posting_frequency or "N/A",
            recommended_cta=ctas_str,
            recommended_hashtags=tags_str,
            strengths=strengths_str,
            weaknesses=weaknesses_str,
            opportunities=opps_str,
            risks=risks_str
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
                detail=f"AI provider returned invalid JSON format: {str(jde)}"
            )
            
        duration = time.perf_counter() - start_time
        logger.info(
            f"[ReqId: {request_id}] Strategy generated successfully. Provider: {active_provider_name}, Model: {result.get('model')}, ExecTime: {duration:.4f}s"
        )
        
        return make_gateway_response(
            success=True,
            message="Marketing strategy generation completed successfully.",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data={
                "strategy": parsed_data,
                "model": result.get("model", settings.DEFAULT_MODELS.get(active_provider_name))
            }
        )
        
    except FileNotFoundError as fnfe:
        logger.error(f"[ReqId: {request_id}] Prompt template missing: {str(fnfe)}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Prompt template configuration missing error: {str(fnfe)}"
        )
    except AIException as aie:
        duration = time.perf_counter() - start_time
        logger.error(
            f"[ReqId: {request_id}] AI Provider error. Error details: {aie.detail}"
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
            f"[ReqId: {request_id}] Unexpected error in strategy generation. Error: {str(e)}"
        )
        return make_gateway_response(
            success=False,
            message=f"Unexpected strategy generation error: {str(e)}",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data=None
        )
