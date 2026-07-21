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

            import re
            import json

            # 1. Parse/Extract brand name from prompt
            brand_name = "Acme Inc"
            brand_match = re.search(r'(?:Brand Name:|Analyzing brand\s*\'|Brand Name:\s*)([^\'\n\r\t]+)', prompt, re.IGNORECASE)
            if brand_match:
                brand_name = brand_match.group(1).strip()
                brand_name = brand_name.replace("'", "").replace('"', '').strip()
            else:
                strat_match = re.search(r'Strategy Name:\s*(.*)', prompt, re.IGNORECASE)
                if strat_match:
                    brand_name = strat_match.group(1).replace("Strategy", "").replace("Plan", "").strip()

            if not brand_name or brand_name.lower() in ["n/a", "unknown"]:
                brand_name = "Acme Inc"

            # 2. Detect industry from prompt context
            industry = "default"
            prompt_lower = prompt.lower()
            if any(k in prompt_lower for k in ["builder", "real estate", "villa", "property", "home", "construction"]):
                industry = "real_estate"
            elif any(k in prompt_lower for k in ["restaurant", "leaf", "food", "dining", "dish", "chef", "menu"]):
                industry = "restaurant"
            elif any(k in prompt_lower for k in ["dental", "clinic", "dentist", "smile", "tooth", "teeth"]):
                industry = "dentistry"
            elif any(k in prompt_lower for k in ["fitness", "gym", "workout", "trainer", "elite fitness"]):
                industry = "fitness"

            # 3. Handle Caption generation mock
            if "Social Media Copywriter" in prompt or "engaging caption" in prompt:
                platform = "Instagram"
                if "Target Platform: LinkedIn" in prompt:
                    platform = "LinkedIn"
                elif "Target Platform: X (Twitter)" in prompt:
                    platform = "X (Twitter)"
                elif "Target Platform: Twitter" in prompt:
                    platform = "Twitter"
                elif "Target Platform: Facebook" in prompt:
                    platform = "Facebook"
                elif "Target Platform: Threads" in prompt:
                    platform = "Threads"
                elif "Target Platform: YouTube" in prompt:
                    platform = "YouTube"
                elif "Target Platform: Instagram" in prompt:
                    platform = "Instagram"

                tone = "Professional"
                tone_match = re.search(r'- Tone:\s*(\w+)', prompt)
                if tone_match:
                    tone = tone_match.group(1)

                language = "English"
                lang_match = re.search(r'- Language:\s*(\w+)', prompt)
                if lang_match:
                    language = lang_match.group(1)

                if industry == "real_estate":
                    headline = "Building for Generations"
                    caption = f"From foundation to final paint, {brand_name} is dedicated to unparalleled craftsmanship. Discover how we turn blueprints into premium landmarks."
                    cta = "Contact our building advisors today to discuss your project."
                    keywords = ["construction", "real estate", "builders"]
                    hashtags = ["#Construction", "#RealEstate", f"#{brand_name.replace(' ', '')}"]
                    emojis = "🏢🏗🏡"
                elif industry == "restaurant":
                    headline = "Grown with Care, Served with Love"
                    caption = f"At {brand_name}, we believe great meals start with healthy organic soil. Taste the vibrant culinary flavors of our freshly harvested farm-to-table specials."
                    cta = "Reserve your table for tonight's dining experience."
                    keywords = ["organic dining", "farm to table", "restaurant"]
                    hashtags = ["#FarmToTable", "#OrganicFood", f"#{brand_name.replace(' ', '')}"]
                    emojis = "🥗🌾🍷"
                elif industry == "dentistry":
                    headline = "Your Smile, Our Priority"
                    caption = f"A healthy smile changes everything. At {brand_name}, we make standard check-ups comfortable, gentle, and completely anxiety-free."
                    cta = "Book your check-up online today."
                    keywords = ["dentistry", "dental clinic", "oral care"]
                    hashtags = ["#DentalHealth", "#SmileCare", f"#{brand_name.replace(' ', '')}"]
                    emojis = "🦷✨🏥"
                elif industry == "fitness":
                    headline = "Elevate Your Limits"
                    caption = f"Success starts outside your comfort zone. At {brand_name}, we build form, strength, and community to keep you moving."
                    cta = "Sign up for your free 3-day guest pass today."
                    keywords = ["fitness", "gym strength", "workout"]
                    hashtags = ["#FitnessGoals", "#GymLife", f"#{brand_name.replace(' ', '')}"]
                    emojis = "💪🏋️🚴"
                else:
                    headline = "Unlocking Potential"
                    caption = f"This is a simulated high-quality social media caption for {brand_name}. We design workflows that keep you ahead."
                    cta = "Get started today."
                    keywords = ["efficiency", "workflow", "productivity"]
                    hashtags = ["#Efficiency", "#Success", f"#{brand_name.replace(' ', '')}"]
                    emojis = "🚀✨📈"

                mock_caption = {
                    "Platform": platform,
                    "Headline": headline,
                    "Caption": caption,
                    "Call To Action": cta,
                    "Primary Keywords": keywords,
                    "Suggested Hashtags": hashtags,
                    "Emoji Recommendation": emojis,
                    "Tone": tone,
                    "Language": language,
                    "Estimated Character Count": len(caption) + 50
                }
                return {
                    "text": json.dumps(mock_caption),
                    "model": self.model,
                    "raw_response": {"mock": True, "prompt": prompt}
                }

            # 4. Handle Content Calendar generation mock
            elif "content calendar" in prompt or "calendar generation" in prompt or "monthly calendar" in prompt:
                month_val = 7
                year_val = 2026
                month_names = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
                for i, m_name in enumerate(month_names, 1):
                    if m_name in prompt:
                        month_val = i
                        break
                year_match = re.search(r'\b(202[4-9]|2030)\b', prompt)
                if year_match:
                    year_val = int(year_match.group(1))

                if industry == "real_estate":
                    topic_1 = f"Choosing the right foundation for custom homes with {brand_name}"
                    title_1 = "Foundations That Stand the Test of Time"
                    pillar_1 = "Construction Excellence"
                    cta_1 = "Schedule an engineering consultation"

                    topic_2 = f"Inside the design phase of a modern property with {brand_name}"
                    title_2 = "Modern Architecture: Concept to Blueprint"
                    pillar_2 = "Design Innovations"
                    cta_2 = "View our active portfolio"
                elif industry == "restaurant":
                    topic_1 = f"Behind our signature organic herb garden at {brand_name}"
                    title_1 = "Harvest to Plate: Organic Culinary Difference"
                    pillar_1 = "Farm-to-Table Freshness"
                    cta_1 = "Book your table online"

                    topic_2 = f"Chef's secrets for preparing the perfect seasonal dish"
                    title_2 = "Crafting Fresh Autumn Flavors"
                    pillar_2 = "Chef Specials"
                    cta_2 = "Check out our full menu"
                elif industry == "dentistry":
                    topic_1 = f"Preventing tooth decay with pediatric tips from {brand_name}"
                    title_1 = "Early Care: Building Strong Smiles"
                    pillar_1 = "Preventative Care"
                    cta_1 = "Schedule a consultation today"

                    topic_2 = f"How modern safe whitening brightens your teeth at {brand_name}"
                    title_2 = "Safe & Gentle Cosmetic Transformations"
                    pillar_2 = "Cosmetic dentistry"
                    cta_2 = "Book a scan appointment"
                elif industry == "fitness":
                    topic_1 = f"Correcting squat posture under supervision at {brand_name}"
                    title_1 = "Perfect Squat Form: Safe Lift Hacks"
                    pillar_1 = "Workout Techniques"
                    cta_1 = "Get your guest pass"

                    topic_2 = f"Why high intensity workouts build heart health"
                    title_2 = "HIIT Routines: Burn and Recover"
                    pillar_2 = "Functional training"
                    cta_2 = "Try our class schedule"
                else:
                    topic_1 = f"How to automate workflows for {brand_name}"
                    title_1 = "Save 10+ Hours/Week with Smart Automation"
                    pillar_1 = "AI & Productivity Tips"
                    cta_1 = "Start your free trial today"

                    topic_2 = f"Stop manual operations. Let AI orchestrate your schedule."
                    title_2 = "Under the Hood: Performance Optimization"
                    pillar_2 = "Tech Architecture"
                    cta_2 = "Join the waitlist"

                mock_posts = [
                    {
                        "Date": f"{year_val:04d}-{month_val:02d}-02",
                        "Platform": "LinkedIn",
                        "Topic": topic_1,
                        "Working Title": title_1,
                        "Content Pillar": pillar_1,
                        "Campaign": "Launch Campaign",
                        "Goal": "Lead Generation",
                        "Content Type": "Educational",
                        "Post Format": "Text",
                        "Suggested CTA": cta_1,
                        "Priority": "High"
                    },
                    {
                        "Date": f"{year_val:04d}-{month_val:02d}-10",
                        "Platform": "Instagram",
                        "Topic": topic_2,
                        "Working Title": title_2,
                        "Content Pillar": pillar_2,
                        "Campaign": "Behind the Scenes",
                        "Goal": "Brand Awareness",
                        "Content Type": "Promotional",
                        "Post Format": "Carousel",
                        "Suggested CTA": cta_2,
                        "Priority": "High"
                    }
                ]
                return {
                    "text": json.dumps(mock_posts),
                    "model": self.model,
                    "raw_response": {"mock": True, "prompt": prompt}
                }

            # 5. Handle Strategy generation mock
            elif "strategy_name" in prompt or "marketing strategy" in prompt or "strategy generation" in prompt:
                if industry == "real_estate":
                    strategy_name = f"{brand_name} Digital Footprint & Authority Strategy"
                    business_goal = "Establish market leadership in custom luxury building projects within 12 months."
                    marketing_goal = "Drive qualified design consultations and building contract leads."
                    platforms = ["LinkedIn", "Instagram"]
                    pillars = ["Construction Excellence", "Design Innovations", "Customer Journeys"]
                    campaign_name = "Blueprints to Reality"
                    campaign_desc = "Weekly progress updates showcasing advanced craftsmanship and luxury properties."
                elif industry == "restaurant":
                    strategy_name = f"{brand_name} Taste & Organic Culinary Strategy"
                    business_goal = "Establish the practice as the premier community destination for farm-to-table dining."
                    marketing_goal = "Drive consistent table reservations and weekend dining walk-ins."
                    platforms = ["Instagram", "Facebook"]
                    pillars = ["Farm-to-Table Freshness", "Chef Specials", "Sustainability Actions"]
                    campaign_name = "Soil to Plate"
                    campaign_desc = "Vibrant video spotlights tracking fresh local organic supplies arriving at our kitchen."
                elif industry == "dentistry":
                    strategy_name = f"{brand_name} Patient Trust & Care Strategy"
                    business_goal = "Position the practice as the leading family and cosmetic clinic in the region."
                    marketing_goal = "Increase new patient checkup registrations by 20% in Q3."
                    platforms = ["Facebook", "Instagram"]
                    pillars = ["Preventative Care", "Cosmetic dentistry", "Patient Comfort Care"]
                    campaign_name = "Comfort Smiles"
                    campaign_desc = "Patient comfort spotlights highlighting gentle therapies and state-of-the-art procedures."
                elif industry == "fitness":
                    strategy_name = f"{brand_name} High-Performance Marketing Strategy"
                    business_goal = "Boost monthly functional training memberships and personal training enrollments."
                    marketing_goal = "Position the gym as the leading regional hub for functional fitness."
                    platforms = ["Instagram", "YouTube"]
                    pillars = ["Workout Techniques", "Trainer Spotlights", "Member Success Stories"]
                    campaign_name = "Limits Redefined"
                    campaign_desc = "Member success transformations showcasing functional techniques and nutrition advice."
                else:
                    strategy_name = f"{brand_name} Growth & Automation Strategy"
                    business_goal = "Establish market authority and scale customer engagement."
                    marketing_goal = "Drive organic trial signups and user registrations."
                    platforms = ["LinkedIn", "Twitter"]
                    pillars = ["AI Productivity", "Tech Architecture", "Growth Hacks"]
                    campaign_name = "Automation reboot"
                    campaign_desc = "Showcase daily automation workflows saving time and budget."

                mock_strategy = {
                    "strategy_name": strategy_name,
                    "business_goal": business_goal,
                    "marketing_goal": marketing_goal,
                    "recommended_platforms": platforms,
                    "content_pillars": pillars,
                    "campaign_ideas": [
                        {
                            "name": campaign_name,
                            "description": campaign_desc,
                            "duration": "3 months",
                            "channels": platforms
                        }
                    ],
                    "posting_frequency": "3 times per week per platform",
                    "recommended_formats": ["Video Reels", "Educational Threads"],
                    "tone_guidelines": ["Speak with clear authority, remain friendly and approachable."],
                    "audience_segments": ["Target demographic segments interested in quality and reliability."],
                    "cta_strategy": ["Direct clear actions pointing to booking or trial portals."],
                    "hashtags_strategy": ["Use 3-5 high-value focused tags, avoid tag stuffing."],
                    "kpis": ["Direct conversion rate of reservations/bookings/trials"],
                    "growth_recommendations": ["Collaborate with local brand partners to expand digital authority."],
                    "risk_considerations": ["Shifts in target ad platform API rules."],
                    "confidence_score": 95
                }
                return {
                    "text": json.dumps(mock_strategy),
                    "model": self.model,
                    "raw_response": {"mock": True, "prompt": prompt}
                }

            # 6. Handle Brand Intelligence generation mock
            elif "JSON" in prompt or "brand personality" in prompt:
                if industry == "real_estate":
                    summary = f"{brand_name} specializes in premier property development and high-end construction."
                    personality = ["Professional", "Reliable", "Precise", "Quality-focused"]
                    voice = ["Authoritative", "Trustworthy", "Clear"]
                    demographics = "Affluent individuals, custom home builders"
                    behaviors = "Seeking customized solutions, high quality standards"
                    pains = "Unreliable builders, poor structural transparency"
                    problems = ["Project delays", "Low-grade materials", "Lack of clear engineering info"]
                    goals = ["Build custom properties", "Worry-free project management"]
                    objectives = ["Establish brand quality authority"]
                    comp_summary = "Competitors compete on price; this brand differentiates on quality and precision."
                    pillars = ["Construction Excellence", "Design Innovations", "Customer Journeys"]
                    cta_list = ["Book custom property scan consultation"]
                    tags = ["#Construction", "#RealEstate", f"#{brand_name.replace(' ', '')}"]
                elif industry == "restaurant":
                    summary = f"{brand_name} is a premier dining destination focusing on sustainable, organic, farm-to-table culinary experiences."
                    personality = ["Welcoming", "Vibrant", "Eco-conscious", "Artisanal"]
                    voice = ["Warm", "Sensory-rich", "Friendly"]
                    demographics = "Health-conscious locals, couples, culinary enthusiasts"
                    behaviors = "Prefers organic food, values green initiatives"
                    pains = "Lack of genuine organic local foods, process-heavy menus"
                    problems = ["Few healthy menu options nearby", "Lack of farm trace sources"]
                    goals = ["Enjoy healthy artisanal dining", "Discover fresh chef recipes"]
                    objectives = ["Establish regional community dining leadership"]
                    comp_summary = "Competitors offer fast casual; this practice leads in culinary organic freshness."
                    pillars = ["Farm-to-Table Freshness", "Chef Specials", "Sustainability Actions"]
                    cta_list = ["Reserve dinner table online"]
                    tags = ["#FarmToTable", "#OrganicFood", f"#{brand_name.replace(' ', '')}"]
                elif industry == "dentistry":
                    summary = f"{brand_name} provides state-of-the-art preventative, restorative, and cosmetic dental treatments in a relaxing environment."
                    personality = ["Caring", "Gentle", "Professional", "Advanced"]
                    voice = ["Reassuring", "Educational", "Approachable"]
                    demographics = "Families, local residents seeking cosmetic enhancements"
                    behaviors = "Consistently tracks hygiene checkups, values clean aesthetics"
                    pains = "Dental phobia, lack of transparent pricing"
                    problems = ["Painful standard checkups", "Unhappy with teeth stains"]
                    goals = ["Gentle checkups", "Bright confidence-boosting smiles"]
                    objectives = ["Become the most recommended regional clinic practice"]
                    comp_summary = "Competitors lack patient comfort focus; this clinic stands out for gentle care."
                    pillars = ["Preventative Care", "Cosmetic dentistry", "Patient Comfort Care"]
                    cta_list = ["Book dental checkup online"]
                    tags = ["#DentalHealth", "#SmileCare", f"#{brand_name.replace(' ', '')}"]
                elif industry == "fitness":
                    summary = f"{brand_name} is a high-performance training facility offering group classes, personal training, and recovery wellness."
                    personality = ["Motivating", "Energetic", "Discipline-driven", "Community-centric"]
                    voice = ["Empowering", "Direct", "Inspiring"]
                    demographics = "Fitness enthusiasts, local professionals seeking coaching"
                    behaviors = "Tracks weekly exercise routines, attends group workouts"
                    pains = "Loss of workout routine accountability, unguided training"
                    problems = ["Lack of strength progress", "Injury from incorrect form"]
                    goals = ["Build functional strength", "Learn elite trainer techniques"]
                    objectives = ["Establish practice as regional functional training leader"]
                    comp_summary = "Competitors operate self-access gyms; this gym leads in coaching quality."
                    pillars = ["Workout Techniques", "Trainer Spotlights", "Member Success Stories"]
                    cta_list = ["Get free 3-day guest pass"]
                    tags = ["#FitnessGoals", "#GymLife", f"#{brand_name.replace(' ', '')}"]
                else:
                    summary = f"{brand_name} is a leading provider of innovative workspace solutions."
                    personality = ["Innovative", "Reliable", "Confident", "Insightful"]
                    voice = ["Insightful", "Professional yet approachable"]
                    demographics = "SaaS developers, professional social managers"
                    behaviors = "Seeking automation pipelines to optimize daily task flows"
                    pains = "Manual workflow delays, uninspired layouts"
                    problems = ["Manual digital tracking", "Slow template generation"]
                    goals = ["Automate social channels", "Build digital growth resonance"]
                    objectives = ["Differentiate based on AI assistant efficiency features"]
                    comp_summary = "Competitors lack custom integrations; this brand leads in gateway routing speeds."
                    pillars = ["AI Productivity", "Tech Architecture", "Growth Hacks"]
                    cta_list = ["Start free workspace trial"]
                    tags = ["#DigitalGrowth", "#Productivity", f"#{brand_name.replace(' ', '')}"]

                mock_data = {
                    "summary": summary,
                    "brand_personality": personality,
                    "brand_voice": voice,
                    "ideal_customer": {
                        "demographics": demographics,
                        "behaviors": behaviors,
                        "pains": pains
                    },
                    "customer_problems": problems,
                    "customer_goals": goals,
                    "marketing_objectives": objectives,
                    "competitor_summary": comp_summary,
                    "recommended_content_pillars": pillars,
                    "recommended_posting_frequency": "3 times per week per platform",
                    "recommended_cta": cta_list,
                    "recommended_hashtags": tags,
                    "strengths": ["Clear positioning", "Differentiated quality value"],
                    "weaknesses": ["New brand in local market"],
                    "opportunities": ["Rising regional demand for specialized services"],
                    "risks": ["Rapid competitor imitation"],
                    "confidence_score": 95
                }
                return {
                    "text": json.dumps(mock_data),
                    "model": self.model,
                    "raw_response": {"mock": True, "prompt": prompt}
                }

            return {
                "text": f"Simulated response to prompt from Gemini AI model. Target brand '{brand_name}' is fully operational in '{industry}' industry.",
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
