from typing import Optional
from fastapi import Header, HTTPException, status
from config.settings import settings

async def verify_api_token(x_api_token: Optional[str] = Header(None, alias="X-API-Token")):
    if not x_api_token or x_api_token != settings.API_TOKEN:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid or missing API Token"
        )
