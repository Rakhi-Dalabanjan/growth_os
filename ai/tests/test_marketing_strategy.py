import pytest
from fastapi.testclient import TestClient
from main import app
from config.settings import settings

client = TestClient(app)
headers = {"X-API-Token": settings.API_TOKEN}

def test_generate_strategy_endpoint_requires_auth():
    payload = {
        "summary": "Acme is a dev tool.",
        "brand_personality": ["Reliable"]
    }
    response = client.post("/ai/strategy/generate", json=payload)
    assert response.status_code == 401

def test_generate_strategy_endpoint_success():
    payload = {
        "summary": "GrowthOS is an AI social media SaaS.",
        "brand_personality": ["Innovative", "Reliable"],
        "brand_voice": ["Confident", "Insightful"],
        "ideal_customer": {
            "demographics": "Social media managers aged 25-45.",
            "behaviors": "Active posting, automating tasks."
        },
        "customer_problems": ["Burnout", "Slow content creation"],
        "customer_goals": ["Automate calendar scheduling"],
        "marketing_objectives": ["Build developer authority"],
        "competitor_summary": "No AI gateway tools in early competitors.",
        "recommended_content_pillars": ["AI Productivity", "Tech Architecture"],
        "recommended_posting_frequency": "5 times a week",
        "recommended_cta": ["Start free trial"],
        "recommended_hashtags": ["#AI", "#SaaS"],
        "strengths": ["Clean code", "FastAPI integration"],
        "weaknesses": ["New player in market"],
        "opportunities": ["High automation demand"],
        "risks": ["API policy updates"]
    }
    response = client.post("/ai/strategy/generate", json=payload, headers=headers)
    assert response.status_code == 200
    
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["message"] == "Marketing strategy generation completed successfully."
    assert json_data["provider"] == settings.DEFAULT_AI_PROVIDER
    
    strategy = json_data["data"]["strategy"]
    assert "strategy_name" in strategy
    assert "business_goal" in strategy
    assert "marketing_goal" in strategy
    assert "recommended_platforms" in strategy
    assert "campaign_ideas" in strategy
    assert len(strategy["campaign_ideas"]) > 0
    assert 0 <= strategy["confidence_score"] <= 100
