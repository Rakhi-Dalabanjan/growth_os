from datetime import datetime, timezone
from typing import Any, Optional
from pydantic import BaseModel, Field

class APIResponse(BaseModel):
    success: bool
    message: str
    data: Optional[Any] = None
    timestamp: str = Field(default_factory=lambda: datetime.now(timezone.utc).isoformat())

def make_response(success: bool, message: str, data: Optional[Any] = None) -> dict:
    return APIResponse(success=success, message=message, data=data).model_dump()
