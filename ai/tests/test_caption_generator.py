import pytest
from fastapi.testclient import TestClient
from main import app
from config.settings import settings

client = TestClient(app)
headers = {"X-API-Token": settings.API_TOKEN}

def test_generate_caption_endpoint_requires_auth():
    payload = {
        "brand_intelligence": {"summary": "A cool brand", "brand_personality": ["Fun"]},
        "marketing_strategy": {"strategy_name": "My strategy"},
        "content_calendar": {"title": "Cool Post", "topic": "AI", "platform": "Instagram"},
        "tone": "Friendly",
        "language": "English"
    }
    response = client.post("/ai/captions/generate", json=payload)
    assert response.status_code == 401

def test_generate_caption_endpoint_success():
    payload = {
        "brand_intelligence": {
            "summary": "GrowthOS is an AI Social Media Operating System.",
            "brand_personality": ["Innovative", "Empowering"],
            "brand_voice": ["Confident", "Insightful"],
            "brand_tone": "Inspirational",
            "languages": ["English"]
        },
        "marketing_strategy": {
            "strategy_name": "GrowthOS Authority Strategy",
            "business_goal": "Establish authority in the AI space.",
            "marketing_goal": "Drive organic trial signups.",
            "recommended_platforms": ["LinkedIn", "Twitter", "Instagram"]
        },
        "content_calendar": {
            "title": "Save 10+ Hours/Week with Smart Automation",
            "topic": "How to automate content workflows",
            "platform": "LinkedIn",
            "content_pillar": "AI Productivity Tips",
            "campaign_name": "Launch Campaign",
            "goal": "Lead Generation",
            "content_type": "Educational",
            "post_format": "Text",
            "priority": "High",
            "notes": "Include a link to sign up"
        },
        "tone": "Professional",
        "language": "English"
    }
    response = client.post("/ai/captions/generate", json=payload, headers=headers)
    assert response.status_code == 200
    
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["message"] == "Caption generated successfully."
    assert json_data["provider"] == settings.DEFAULT_AI_PROVIDER
    
    caption_data = json_data["data"]["caption"]
    assert caption_data["Platform"] == "LinkedIn"
    assert "Headline" in caption_data
    assert "Caption" in caption_data
    assert "Call To Action" in caption_data
    assert "Primary Keywords" in caption_data
    assert "Suggested Hashtags" in caption_data
    assert "Emoji Recommendation" in caption_data
    assert caption_data["Tone"] == "Professional"
    assert caption_data["Language"] == "English"
    assert "Estimated Character Count" in caption_data
