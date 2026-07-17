from fastapi import APIRouter, Request, Depends
from schemas.response import make_response
from utils.security import verify_api_token

router = APIRouter(dependencies=[Depends(verify_api_token)])

@router.post("/ping")
async def ping(request: Request):
    try:
        body = await request.json()
    except Exception:
        body = {}
    return make_response(
        success=True,
        message="Pong",
        data={"ping": "pong", "received": body}
    )

@router.post("/echo")
async def echo(request: Request):
    try:
        body = await request.json()
    except Exception:
        body = {}
    return make_response(
        success=True,
        message="Echo",
        data=body
    )
