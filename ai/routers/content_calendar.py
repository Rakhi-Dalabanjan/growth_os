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

logger = logging.getLogger("ai_content_calendar")
router = APIRouter(prefix="/ai/calendar", dependencies=[Depends(verify_api_token)])

class CalendarGenerateRequest(BaseModel):
    strategy_name: str = Field(..., description="Name of the marketing strategy")
    business_goal: str = Field(..., description="Business objectives")
    marketing_goal: str = Field(..., description="Marketing objectives")
    posting_frequency: str = Field(..., description="Recommended posting frequency")
    platforms: List[str] = Field(..., description="Target platforms")
    pillars: List[str] = Field(..., description="Content pillars")
    campaigns: List[Dict[str, Any]] = Field(..., description="Campaign ideas and details")
    month: int = Field(..., ge=1, le=12, description="Target calendar month (1-12)")
    year: int = Field(..., description="Target calendar year")

def load_prompt_template() -> str:
    path = os.path.join(os.path.dirname(os.path.dirname(__file__)), "prompts", "content_calendar.txt")
    if not os.path.exists(path):
        raise FileNotFoundError(f"Content calendar prompt template not found at {path}")
    with open(path, "r", encoding="utf-8") as f:
        return f.read()

@router.post("/generate")
async def generate_calendar(req: CalendarGenerateRequest):
    start_time = time.perf_counter()
    request_id = uuid.uuid4().hex
    active_provider_name = settings.DEFAULT_AI_PROVIDER
    
    logger.info(
        f"[ReqId: {request_id}] Generating content calendar for Month: {req.month}, Year: {req.year}. Provider: {active_provider_name}"
    )
    
    try:
        template = load_prompt_template()
        
        # Serialize fields into readable formatted lines for prompt injection
        platforms_str = ", ".join(req.platforms)
        pillars_str = ", ".join(req.pillars)
        
        campaigns_str_list = []
        for c in req.campaigns:
            name = c.get("name", "Unnamed Campaign")
            desc = c.get("description", "")
            campaigns_str_list.append(f"{name} ({desc})")
        campaigns_str = "; ".join(campaigns_str_list) if campaigns_str_list else "N/A"
        
        month_names = [
            "", "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ]
        month_name = month_names[req.month] if 1 <= req.month <= 12 else str(req.month)

        # Format the prompt
        prompt = template.format(
            strategy_name=req.strategy_name,
            business_goal=req.business_goal,
            marketing_goal=req.marketing_goal,
            posting_frequency=req.posting_frequency,
            platforms=platforms_str,
            pillars=pillars_str,
            campaigns=campaigns_str,
            month=month_name,
            year=str(req.year)
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
            f"[ReqId: {request_id}] Content calendar generated successfully. Provider: {active_provider_name}, Model: {result.get('model')}, ExecTime: {duration:.4f}s"
        )
        
        return make_gateway_response(
            success=True,
            message="Content calendar generation completed successfully.",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data={
                "calendar": parsed_data,
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
            f"[ReqId: {request_id}] Unexpected error in calendar generation. Error: {str(e)}"
        )
        return make_gateway_response(
            success=False,
            message=f"Unexpected calendar generation error: {str(e)}",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data=None
        )
