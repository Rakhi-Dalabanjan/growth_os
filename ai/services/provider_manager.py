from typing import Dict, Type
from config.settings import settings
from providers.base import BaseAIProvider
from providers.openai import OpenAIProvider
from providers.gemini import GeminiProvider
from providers.claude import ClaudeProvider
from exceptions.gateway_exceptions import InvalidProviderError

class AIProviderManager:
    _providers: Dict[str, Type[BaseAIProvider]] = {
        "gemini": GeminiProvider,
        "openai": OpenAIProvider,
        "claude": ClaudeProvider
    }

    @classmethod
    def get_provider(cls, name: str = None) -> BaseAIProvider:
        provider_name = name or settings.DEFAULT_AI_PROVIDER
        if not provider_name:
            raise InvalidProviderError("No default AI provider configured in settings.")
        
        provider_name = provider_name.lower()
        if provider_name not in cls._providers:
            raise InvalidProviderError(f"AI provider '{provider_name}' is invalid or unsupported.")

        provider_class = cls._providers[provider_name]
        try:
            instance = provider_class()
        except Exception as e:
            raise InvalidProviderError(f"Failed to instantiate AI provider '{provider_name}': {str(e)}")

        return instance
