import asyncio
import httpx
from typing import Dict, Any
from config.settings import settings
from providers.base import BaseAIProvider
from exceptions.gateway_exceptions import (
    MissingAPIKeyError, ProviderOfflineError, ProviderAPIError
)

class GeminiProvider(BaseAIProvider):
    def __init__(self):
        self.api_key = settings.GEMINI_API_KEY
        self.model = settings.DEFAULT_MODELS.get("gemini", "gemini-1.5-flash")
        self.is_mock = not self.api_key or self.api_key.startswith("mock")

    async def generate_text(self, prompt: str, timeout: float = 10.0) -> Dict[str, Any]:
        if self.is_mock:
            # Simulate network delay and return mock text
            await asyncio.sleep(0.1)
            
            if "strategy_name" in prompt or "marketing strategy" in prompt or "strategy generation" in prompt:
                mock_strategy = {
                    "strategy_name": "GrowthOS Market Authority & Automation Strategy",
                    "business_goal": "Establish market leadership in the AI-powered social media tooling sector within 6 months.",
                    "marketing_goal": "Drive 10,000 organic product trials by building a highly engaged developer and creator audience.",
                    "recommended_platforms": ["LinkedIn", "Twitter", "Instagram"],
                    "content_pillars": ["AI Productivity", "Tech Architecture", "Growth Hacks"],
                    "campaign_ideas": [
                        {
                            "name": "The 30-Day Social Automation Challenge",
                            "description": "Showcase daily automation workflows showing how developers can save 10+ hours per week using GrowthOS.",
                            "duration": "30 days",
                            "channels": ["Twitter", "LinkedIn"]
                        },
                        {
                            "name": "Behind the Code",
                            "description": "Weekly deep dives showing how we built our FastAPI AI Gateway, highlighting raw engineering and clean code.",
                            "duration": "Continuous",
                            "channels": ["LinkedIn"]
                        }
                    ],
                    "posting_frequency": "5 times a week on LinkedIn, 3 times a day on Twitter, 3 reels per week on Instagram",
                    "recommended_formats": ["LinkedIn Carousels", "Twitter/X Threads", "Short-Form Video Reels"],
                    "tone_guidelines": [
                        "Speak with clear technical authority but remain friendly.",
                        "Avoid buzzwords; explain exact mechanisms.",
                        "Write directly to build connection."
                    ],
                    "audience_segments": ["Indie Hackers", "Developer Advocates", "Social Media Agencies"],
                    "cta_strategy": [
                        "Direct links to start a free trial (no credit card required)",
                        "Inviting followers to join the Discord developer community"
                    ],
                    "hashtags_strategy": [
                        "Use 2-3 focused tags like #BuildInPublic and #AIWorkflow on Twitter.",
                        "Avoid tag stuffing on LinkedIn."
                    ],
                    "kpis": [
                        "Direct click-through rate (CTR) to trial signup page",
                        "Weekly active user (WAU) growth in our beta Discord",
                        "Share rate of carousel and thread content"
                    ],
                    "growth_recommendations": [
                        "Engage actively in comments under major SaaS founders' accounts.",
                        "Publish monthly transparency and build-in-public growth metrics."
                    ],
                    "risk_considerations": [
                        "Platform API rate limit changes could affect automated pipelines.",
                        "High dependence on AI quality demands constant monitoring."
                    ],
                    "confidence_score": 92
                }
                import json
                return {
                    "text": json.dumps(mock_strategy),
                    "model": self.model,
                    "raw_response": {"mock": True, "prompt": prompt}
                }

            elif "JSON" in prompt or "brand personality" in prompt:
                # Extract brand name dynamically from prompt to make mock more realistic
                brand_name = "GrowthOS"
                for line in prompt.split("\n"):
                    if "- Brand Name:" in line:
                        brand_name = line.split("- Brand Name:")[-1].strip()
                        break

                mock_data = {
                    "summary": f"{brand_name} is a revolutionary digital presence and marketing solution designed to automate workflows and scale target audience engagement.",
                    "brand_personality": ["Innovative", "Empowering", "Reliable", "Creative"],
                    "brand_voice": ["Confident", "Professional yet approachable", "Insightful"],
                    "ideal_customer": {
                        "demographics": "Target audience segment and key client base interested in professional growth.",
                        "behaviors": "Consistently active on digital channels, seeking efficient scheduling and optimization tools.",
                        "pains": "Struggling with content consistency, platform growth strategy, and actionable resonance metrics."
                    },
                    "customer_problems": [
                        "Digital publishing is manual and tedious",
                        "Content generation is slow and uninspired",
                        "Difficult to build brand positioning and resonance"
                    ],
                    "customer_goals": [
                        "Automate content calendar strategy",
                        "Create high-quality copy in seconds",
                        "Boost engagement rates with smart timing recommendations"
                    ],
                    "marketing_objectives": [
                        "Build community authority on professional networks",
                        "Drive organic engagement via high-value content",
                        f"Establish {brand_name} as a market leader in this domain"
                    ],
                    "competitor_summary": f"Major competitors are traditional platforms that lack deep customization and intelligent recommendations tailored for {brand_name}.",
                    "recommended_content_pillars": [
                        "Industry insights & Professional tips",
                        "Growth & optimization case studies",
                        f"{brand_name} Project Spotlights"
                    ],
                    "recommended_posting_frequency": "5 times per week across target professional networks",
                    "recommended_cta": [
                        "Explore the core offering today",
                        f"Get started with {brand_name}",
                        f"Join the {brand_name} waitlist and community"
                    ],
                    "recommended_hashtags": ["#DigitalGrowth", f"#{brand_name.replace(' ', '')}", "#Productivity", "#Strategy"],
                    "strengths": [
                        "Modern UI with glassmorphism design",
                        "Seamless Laravel-FastAPI integration",
                        "Robust, test-driven code foundation"
                    ],
                    "weaknesses": [
                        "New brand positioning in a highly saturated market",
                        "Initial platform scaling under active development"
                    ],
                    "opportunities": [
                        "Rising global demand for workflow automation",
                        "Direct API integrations with emerging platforms"
                    ],
                    "risks": [
                        "Frequent shifts in platform API terms of service",
                        "Aggressive feature copying from established legacy competitors"
                    ],
                    "confidence_score": 95
                }
                import json
                return {
                    "text": json.dumps(mock_data),
                    "model": self.model,
                    "raw_response": {"mock": True, "prompt": prompt}
                }

            return {
                "text": f"Simulated response to prompt '{prompt}' from Gemini AI model: The sky is blue and GrowthOS AI Gateway is fully operational.",
                "model": self.model,
                "raw_response": {"mock": True, "prompt": prompt}
            }

        if not self.api_key:
            raise MissingAPIKeyError("Gemini API key is not configured in environment variables.")

        url = f"https://generativelanguage.googleapis.com/v1beta/models/{self.model}:generateContent?key={self.api_key}"
        headers = {"Content-Type": "application/json"}
        payload = {
            "contents": [{
                "parts": [{"text": prompt}]
            }]
        }

        async with httpx.AsyncClient() as client:
            try:
                response = await client.post(url, json=payload, headers=headers, timeout=timeout)
            except httpx.TimeoutException:
                raise ProviderOfflineError("Connection to Gemini API timed out.")
            except Exception as e:
                raise ProviderOfflineError(f"Failed to connect to Gemini API: {str(e)}")

            if response.status_code != 200:
                raise ProviderAPIError(
                    f"Gemini API returned status code {response.status_code}: {response.text}",
                    status_code=response.status_code
                )

            try:
                data = response.json()
                text = data["candidates"][0]["content"]["parts"][0]["text"]
                return {
                    "text": text,
                    "model": self.model,
                    "raw_response": data
                }
            except Exception as e:
                raise ProviderAPIError(f"Failed to parse Gemini API JSON response: {str(e)}")

    async def health_check(self) -> bool:
        if not self.api_key:
            return False
        if self.is_mock:
            return True

        url = f"https://generativelanguage.googleapis.com/v1beta/models/{self.model}:generateContent?key={self.api_key}"
        headers = {"Content-Type": "application/json"}
        payload = {
            "contents": [{
                "parts": [{"text": "Ping healthcheck"}]
            }]
        }
        async with httpx.AsyncClient() as client:
            try:
                res = await client.post(url, json=payload, headers=headers, timeout=2.0)
                return res.status_code == 200
            except Exception:
                return False

    def get_model_info(self) -> Dict[str, Any]:
        return {
            "provider": "gemini",
            "model": self.model,
            "mode": "mock" if self.is_mock else "live"
        }
