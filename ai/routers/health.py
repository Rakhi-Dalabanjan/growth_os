import time
from fastapi import APIRouter
from schemas.response import make_response
from config.settings import settings

router = APIRouter()

START_TIME = time.time()

@router.get("/health")
async def health():
    uptime = time.time() - START_TIME
    return make_response(
        success=True,
        message="Health Check",
        data={
            "status": "online",
            "version": settings.VERSION,
            "uptime": round(uptime, 2)
        }
    )
