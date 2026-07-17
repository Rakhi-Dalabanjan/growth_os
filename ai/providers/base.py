from abc import ABC, abstractmethod
from typing import Dict, Any

class BaseAIProvider(ABC):
    @abstractmethod
    async def generate_text(self, prompt: str, timeout: float = 10.0) -> Dict[str, Any]:
        """
        Generate text response from a prompt.
        Should return a dictionary:
        {
            "text": str,
            "model": str,
            "raw_response": Any
        }
        """
        pass

    @abstractmethod
    async def health_check(self) -> bool:
        """
        Check connection health of the provider.
        Returns:
            bool: True if the provider is reachable and active.
        """
        pass

    @abstractmethod
    def get_model_info(self) -> Dict[str, Any]:
        """
        Return the provider's active model information.
        Returns:
            dict: { "provider": str, "model": str }
        """
        pass
