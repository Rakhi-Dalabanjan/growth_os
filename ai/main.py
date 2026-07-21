from fastapi import FastAPI
from config.settings import settings
from routers import communication, health, gateway, brand_intelligence, marketing_strategy, content_calendar, caption_generator
from schemas.response import make_response

app = FastAPI(
    title=settings.PROJECT_NAME,
    version=settings.VERSION,
    debug=settings.DEBUG
)

# Register routers
app.include_router(communication.router)
app.include_router(health.router)
app.include_router(gateway.router)
app.include_router(brand_intelligence.router)
app.include_router(marketing_strategy.router)
app.include_router(content_calendar.router)
app.include_router(caption_generator.router)

@app.get("/")
async def root():
    return make_response(
        success=True,
        message="GrowthOS AI Service",
        data=None
    )
