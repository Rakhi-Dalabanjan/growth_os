from datetime import datetime, timezone
from typing import Any, Optional
from pydantic import BaseModel, Field

class TestPromptRequest(BaseModel):
    prompt: str = Field(..., description="Prompt query to send to the AI provider")

class GatewayResponse(BaseModel):
    success: bool
    message: str
    provider: str
    execution_time: float
    data: Optional[Any] = None
    timestamp: str = Field(default_factory=lambda: datetime.now(timezone.utc).isoformat())

def make_gateway_response(
    success: bool,
    message: str,
    provider: str,
    execution_time: float,
    data: Optional[Any] = None
) -> dict:
    return GatewayResponse(
        success=success,
        message=message,
        provider=provider,
        execution_time=execution_time,
        data=data
    ).model_dump()
