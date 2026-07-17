import time
import uuid
import logging
from typing import Dict, Any
from fastapi import APIRouter, Depends, HTTPException, status
from config.settings import settings
from utils.security import verify_api_token
from services.provider_manager import AIProviderManager
from schemas.gateway_schema import TestPromptRequest, make_gateway_response
from exceptions.gateway_exceptions import AIException

logger = logging.getLogger("ai_gateway")
router = APIRouter(prefix="/ai", dependencies=[Depends(verify_api_token)])

@router.get("/providers")
async def get_providers():
    start_time = time.perf_counter()
    request_id = uuid.uuid4().hex
    
    try:
        active_provider_name = settings.DEFAULT_AI_PROVIDER
        active_provider = AIProviderManager.get_provider(active_provider_name)
        active_info = active_provider.get_model_info()
        
        providers_list = []
        for name in settings.SUPPORTED_PROVIDERS:
            try:
                prov = AIProviderManager.get_provider(name)
                info = prov.get_model_info()
                providers_list.append({
                    "name": name,
                    "installed": True,
                    "status": "active" if name == active_provider_name else "inactive",
                    "mode": info.get("mode", "live") if name == active_provider_name else "not_configured"
                })
            except NotImplementedError:
                providers_list.append({
                    "name": name,
                    "installed": True,
                    "status": "not_implemented",
                    "mode": "unimplemented"
                })
            except Exception:
                providers_list.append({
                    "name": name,
                    "installed": True,
                    "status": "error",
                    "mode": "unknown"
                })

        duration = time.perf_counter() - start_time
        logger.info(
            f"[ReqId: {request_id}] Providers query success. Provider: gateway, ExecTime: {duration:.4f}s"
        )
        
        return make_gateway_response(
            success=True,
            message="Available AI providers retrieved successfully.",
            provider="gateway",
            execution_time=round(duration, 4),
            data={
                "active_provider": active_provider_name,
                "providers": providers_list
            }
        )
    except Exception as e:
        duration = time.perf_counter() - start_time
        logger.error(
            f"[ReqId: {request_id}] Providers query failed. Error: {str(e)}, ExecTime: {duration:.4f}s"
        )
        return make_gateway_response(
            success=False,
            message=f"Failed to query providers: {str(e)}",
            provider="gateway",
            execution_time=round(duration, 4),
            data=None
        )

@router.post("/test")
async def run_test_prompt(req: TestPromptRequest):
    start_time = time.perf_counter()
    request_id = uuid.uuid4().hex
    active_provider_name = settings.DEFAULT_AI_PROVIDER
    
    logger.info(
        f"[ReqId: {request_id}] Running test prompt. Provider: {active_provider_name}"
    )
    
    try:
        provider = AIProviderManager.get_provider(active_provider_name)
        # Call provider text generation
        result = await provider.generate_text(req.prompt, timeout=settings.AI_TIMEOUT)
        
        duration = time.perf_counter() - start_time
        logger.info(
            f"[ReqId: {request_id}] Test prompt execution success. Provider: {active_provider_name}, ExecTime: {duration:.4f}s"
        )
        
        return make_gateway_response(
            success=True,
            message="Test prompt generated successfully.",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data=result
        )
        
    except NotImplementedError as e:
        duration = time.perf_counter() - start_time
        logger.warning(
            f"[ReqId: {request_id}] Provider execution not implemented. Provider: {active_provider_name}, ExecTime: {duration:.4f}s"
        )
        return make_gateway_response(
            success=False,
            message=f"Feature not implemented for active provider: {str(e)}",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data=None
        )
    except AIException as e:
        duration = time.perf_counter() - start_time
        logger.error(
            f"[ReqId: {request_id}] Gateway error during generation. Provider: {active_provider_name}, Error: {e.detail}, ExecTime: {duration:.4f}s"
        )
        return make_gateway_response(
            success=False,
            message=e.detail,
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data=None
        )
    except Exception as e:
        duration = time.perf_counter() - start_time
        logger.error(
            f"[ReqId: {request_id}] Unexpected error during generation. Provider: {active_provider_name}, Error: {str(e)}, ExecTime: {duration:.4f}s"
        )
        return make_gateway_response(
            success=False,
            message=f"Unexpected generation error: {str(e)}",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data=None
        )

@router.get("/health")
async def get_health():
    start_time = time.perf_counter()
    request_id = uuid.uuid4().hex
    active_provider_name = settings.DEFAULT_AI_PROVIDER
    
    try:
        provider = AIProviderManager.get_provider(active_provider_name)
        is_healthy = await provider.health_check()
        
        duration = time.perf_counter() - start_time
        logger.info(
            f"[ReqId: {request_id}] Gateway health query. Provider: {active_provider_name}, Health: {'healthy' if is_healthy else 'unhealthy'}, ExecTime: {duration:.4f}s"
        )
        
        return make_gateway_response(
            success=True,
            message="AI Gateway health query successful.",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data={
                "gateway_status": "online",
                "provider_health": "online" if is_healthy else "offline",
                "latency_ms": round(duration * 1000, 2),
                "version": settings.VERSION
            }
        )
    except Exception as e:
        duration = time.perf_counter() - start_time
        logger.error(
            f"[ReqId: {request_id}] Gateway health query failed. Provider: {active_provider_name}, Error: {str(e)}, ExecTime: {duration:.4f}s"
        )
        return make_gateway_response(
            success=False,
            message=f"Gateway health fault: {str(e)}",
            provider=active_provider_name,
            execution_time=round(duration, 4),
            data={
                "gateway_status": "online",
                "provider_health": "offline",
                "latency_ms": round(duration * 1000, 2),
                "version": settings.VERSION
            }
        )
