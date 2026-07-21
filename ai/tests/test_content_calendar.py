import pytest
from fastapi.testclient import TestClient
from main import app
from config.settings import settings

client = TestClient(app)
headers = {"X-API-Token": settings.API_TOKEN}

def test_generate_calendar_endpoint_requires_auth():
    payload = {
        "strategy_name": "Test Strategy",
        "business_goal": "Goal",
        "marketing_goal": "Goal",
        "posting_frequency": "3 times/week",
        "platforms": ["LinkedIn"],
        "pillars": ["Pillar"],
        "campaigns": [{"name": "C1", "description": "Desc"}],
        "month": 7,
        "year": 2026
    }
    response = client.post("/ai/calendar/generate", json=payload)
    assert response.status_code == 401

def test_generate_calendar_endpoint_invalid_month():
    payload = {
        "strategy_name": "Test Strategy",
        "business_goal": "Goal",
        "marketing_goal": "Goal",
        "posting_frequency": "3 times/week",
        "platforms": ["LinkedIn"],
        "pillars": ["Pillar"],
        "campaigns": [{"name": "C1", "description": "Desc"}],
        "month": 13, # Invalid month
        "year": 2026
    }
    response = client.post("/ai/calendar/generate", json=payload, headers=headers)
    assert response.status_code == 422

def test_generate_calendar_endpoint_success():
    payload = {
        "strategy_name": "GrowthOS Market Authority & Automation Strategy",
        "business_goal": "Establish market leadership in the AI social media sector.",
        "marketing_goal": "Drive 10,000 trial signups.",
        "posting_frequency": "5 times a week on LinkedIn, 3 times a day on Twitter",
        "platforms": ["LinkedIn", "Twitter", "Instagram"],
        "pillars": ["AI Productivity", "Tech Architecture", "Growth Hacks"],
        "campaigns": [
            {
                "name": "The 30-Day Social Automation Challenge",
                "description": "Showcase daily automation workflows."
            },
            {
                "name": "Behind the Code",
                "description": "Weekly deep dives showing how we built our FastAPI AI Gateway."
            }
        ],
        "month": 7,
        "year": 2026
    }
    response = client.post("/ai/calendar/generate", json=payload, headers=headers)
    assert response.status_code == 200
    
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["message"] == "Content calendar generation completed successfully."
    assert json_data["provider"] == settings.DEFAULT_AI_PROVIDER
    
    calendar = json_data["data"]["calendar"]
    assert isinstance(calendar, list)
    assert len(calendar) > 0
    
    # Check calendar item structure
    first_item = calendar[0]
    assert "Date" in first_item
    assert "Platform" in first_item
    assert "Topic" in first_item
    assert "Working Title" in first_item
    assert "Content Pillar" in first_item
    assert "Campaign" in first_item
    assert "Goal" in first_item
    assert "Content Type" in first_item
    assert "Post Format" in first_item
    assert "Priority" in first_item
