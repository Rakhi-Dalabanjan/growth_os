import asyncio
from typing import Dict, Any
from config.settings import settings
from providers.base import BaseAIProvider

class OpenAIProvider(BaseAIProvider):
    def __init__(self):
        self.api_key = settings.OPENAI_API_KEY
        self.model = settings.DEFAULT_MODELS.get("openai", "gpt-4o-mini")

    async def generate_text(self, prompt: str, timeout: float = 10.0) -> Dict[str, Any]:
        raise NotImplementedError("OpenAI provider is not fully implemented yet.")

    async def health_check(self) -> bool:
        return False

    def get_model_info(self) -> Dict[str, Any]:
        return {
            "provider": "openai",
            "model": self.model,
            "status": "not_implemented"
        }
