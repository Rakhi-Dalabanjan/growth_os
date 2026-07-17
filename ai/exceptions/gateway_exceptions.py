from fastapi import status

class AIException(Exception):
    """Base exception for all AI Gateway operations."""
    status_code: int = status.HTTP_500_INTERNAL_SERVER_ERROR
    detail: str = "Internal AI Gateway Error"

    def __init__(self, detail: str = None, status_code: int = None):
        if detail:
            self.detail = detail
        if status_code is not None:
            self.status_code = status_code
        super().__init__(self.detail)

class InvalidProviderError(AIException):
    """Raised when the requested provider is unknown or unsupported."""
    status_code: int = status.HTTP_400_BAD_REQUEST
    detail: str = "Invalid or unsupported AI provider requested."

class MissingAPIKeyError(AIException):
    """Raised when the API key for a provider is missing or configured with a blank string."""
    status_code: int = status.HTTP_401_UNAUTHORIZED
    detail: str = "AI provider API Key is missing or invalid."

class ProviderOfflineError(AIException):
    """Raised when the AI provider endpoint is unreachable or times out."""
    status_code: int = status.HTTP_504_GATEWAY_TIMEOUT
    detail: str = "AI Provider connection timed out or is offline."

class ProviderAPIError(AIException):
    """Raised when the AI provider responds with a fault status code or bad response model."""
    status_code: int = status.HTTP_502_BAD_GATEWAY
    detail: str = "AI Provider API returned a fault response."
