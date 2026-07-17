import pytest
from fastapi.testclient import TestClient
from main import app
from config.settings import settings

client = TestClient(app)
headers = {"X-API-Token": settings.API_TOKEN}

def test_analyze_brand_endpoint_requires_auth():
    payload = {
        "brand_name": "Acme Tech",
        "business_description": "We make high quality software.",
        "target_audience": "Developers"
    }
    response = client.post("/ai/brand/analyze", json=payload)
    assert response.status_code == 401

def test_analyze_brand_endpoint_success():
    payload = {
        "brand_name": "GrowthOS",
        "business_description": "AI Social Media Operating System.",
        "target_audience": "Social media managers, founders, agency owners aged 25-45.",
        "mission": "Automate social media growth.",
        "vision": "A seamless social workflow.",
        "primary_market": "B2B SaaS",
        "brand_tone": "Confident and professional",
        "languages": ["English"],
        "preferred_words": ["innovative", "reliable"],
        "restricted_words": ["cheap"],
        "competitors": ["Buffer", "Hootsuite"],
        "cta": "Start free trial",
        "brand_colors": ["#2563eb", "#7c3aed"],
        "brand_fonts": ["Inter"]
    }
    response = client.post("/ai/brand/analyze", json=payload, headers=headers)
    assert response.status_code == 200
    
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["message"] == "Brand intelligence analysis completed successfully."
    assert json_data["provider"] == settings.DEFAULT_AI_PROVIDER
    
    # Assert structured intelligence keys
    intel = json_data["data"]["intelligence"]
    assert "summary" in intel
    assert "brand_personality" in intel
    assert "ideal_customer" in intel
    assert "strengths" in intel
    assert "confidence_score" in intel
    assert 0 <= intel["confidence_score"] <= 100
