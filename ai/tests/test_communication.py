from fastapi.testclient import TestClient
from main import app

client = TestClient(app)

def test_read_root():
    response = client.get("/")
    assert response.status_code == 200
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["message"] == "GrowthOS AI Service"
    assert "timestamp" in json_data

def test_read_health():
    response = client.get("/health")
    assert response.status_code == 200
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["data"]["status"] == "online"
    assert "version" in json_data["data"]
    assert "uptime" in json_data["data"]

def test_post_ping():
    payload = {"test": "data"}
    headers = {"X-API-Token": "growthos_ai_secret_token"}
    response = client.post("/ping", json=payload, headers=headers)
    assert response.status_code == 200
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["message"] == "Pong"
    assert json_data["data"]["received"] == payload

def test_post_echo():
    payload = {"foo": "bar", "num": 42}
    headers = {"X-API-Token": "growthos_ai_secret_token"}
    response = client.post("/echo", json=payload, headers=headers)
    assert response.status_code == 200
    json_data = response.json()
    assert json_data["success"] is True
    assert json_data["message"] == "Echo"
    assert json_data["data"] == payload

def test_unauthorized_endpoints():
    # Test ping without token
    response = client.post("/ping", json={"test": "data"})
    assert response.status_code == 401
    assert response.json()["detail"] == "Invalid or missing API Token"

    # Test echo with wrong token
    headers = {"X-API-Token": "wrong_token"}
    response = client.post("/echo", json={"test": "data"}, headers=headers)
    assert response.status_code == 401
    assert response.json()["detail"] == "Invalid or missing API Token"

