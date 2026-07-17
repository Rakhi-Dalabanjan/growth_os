import pytest
from fastapi.testclient import TestClient
from main import app
from config.settings import settings
from services.provider_manager import AIProviderManager
from exceptions.gateway_exceptions import InvalidProviderError
from providers.gemini import GeminiProvider
from providers.openai import OpenAIProvider
from providers.claude import ClaudeProvider

client = TestClient(app)
headers = {"X-API-Token": settings.API_TOKEN}

def test_provider_manager_resolves_valid_providers():
    gemini = AIProviderManager.get_provider("gemini")
    assert isinstance(gemini, GeminiProvider)
    
    openai = AIProviderManager.get_provider("openai")
    assert isinstance(openai, OpenAIProvider)
    
    claude = AIProviderManager.get_provider("claude")
    assert isinstance(claude, ClaudeProvider)

def test_provider_manager_raises_on_invalid_provider():
    with pytest.raises(InvalidProviderError):
        AIProviderManager.get_provider("invalid_provider_name")

@pytest.mark.anyio
async def test_gemini_provider_mock_response():
    gemini = GeminiProvider()
    gemini.is_mock = True
    assert gemini.is_mock is True
    
    res = await gemini.generate_text("Hi", timeout=1.0)
    assert "Simulated response" in res["text"]
    assert res["model"] == settings.DEFAULT_MODELS["gemini"]
    assert res["raw_response"]["mock"] is True

@pytest.mark.anyio
async def test_unimplemented_providers_raise():
    openai = OpenAIProvider()
    with pytest.raises(NotImplementedError):
        await openai.generate_text("Hi")
        
    claude = ClaudeProvider()
    with pytest.raises(NotImplementedError):
        await claude.generate_text("Hi")

def test_get_providers_endpoint():
    response = client.get("/ai/providers", headers=headers)
    assert response.status_code == 200
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["provider"] == "gateway"
    assert "active_provider" in json_data["data"]
    assert len(json_data["data"]["providers"]) == 3

def test_run_test_prompt_endpoint():
    payload = {"prompt": "Hello AI Gateway!"}
    response = client.post("/ai/test", json=payload, headers=headers)
    assert response.status_code == 200
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["provider"] == settings.DEFAULT_AI_PROVIDER
    assert "text" in json_data["data"]

def test_get_health_endpoint():
    response = client.get("/ai/health", headers=headers)
    assert response.status_code == 200
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["data"]["gateway_status"] == "online"
    assert "latency_ms" in json_data["data"]

def test_endpoints_require_authentication():
    response = client.get("/ai/providers")
    assert response.status_code == 401
    
    response = client.get("/ai/health")
    assert response.status_code == 401
    
    response = client.post("/ai/test", json={"prompt": "Hello"})
    assert response.status_code == 401
