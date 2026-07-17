import os
from pathlib import Path
from dotenv import load_dotenv

# Load environmental variables from the parent directory of this config file
env_path = Path(__file__).resolve().parent.parent / '.env'
load_dotenv(dotenv_path=env_path)

class Settings:
    PROJECT_NAME: str = "GrowthOS AI Service"
    VERSION: str = "1.0.0"
    HOST: str = os.getenv("AI_HOST", "127.0.0.1")
    PORT: int = int(os.getenv("AI_PORT", 8080))
    DEBUG: bool = os.getenv("AI_DEBUG", "True").lower() in ("true", "1", "t")
    API_TOKEN: str = os.getenv("AI_API_TOKEN", "growthos_ai_secret_token")

    # AI Provider settings
    DEFAULT_AI_PROVIDER: str = os.getenv("DEFAULT_AI_PROVIDER", "gemini").lower()
    OPENAI_API_KEY: str = os.getenv("OPENAI_API_KEY", "")
    GEMINI_API_KEY: str = os.getenv("GEMINI_API_KEY", "")
    CLAUDE_API_KEY: str = os.getenv("CLAUDE_API_KEY", "")
    AI_TIMEOUT: float = float(os.getenv("AI_TIMEOUT", 10.0))

    # Supported Providers
    SUPPORTED_PROVIDERS = ["gemini", "openai", "claude"]

    # Default Models
    DEFAULT_MODELS = {
        "gemini": "gemini-2.0-flash",
        "openai": "gpt-4o-mini",
        "claude": "claude-3-5-sonnet"
    }

settings = Settings()
