package com.idas.app.screens

import androidx.compose.animation.*
import androidx.compose.animation.core.*
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.scale
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.ui.theme.*

data class OnboardingPage(
    val emoji: String,
    val title: String,
    val subtitle: String,
    val description: String,
    val gradient: List<Color>,
    val features: List<Pair<String, String>>
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun OnboardingScreen(onFinish: () -> Unit) {
    var page by remember { mutableStateOf(0) }

    val pages = listOf(
        OnboardingPage(
            emoji     = "🏥",
            title     = "Willkommen bei IDAS",
            subtitle  = "Ihr persönliches Gesundheitsportal",
            description = "Finden Sie schnell und einfach den richtigen Arzt basierend auf Ihren Symptomen — überall in Deutschland.",
            gradient  = listOf(Color(0xFF1565C0), Color(0xFF1E88E5)),
            features  = listOf(
                "🔍" to "Symptombasierte Arztsuche",
                "📍" to "Ärzte in Ihrer Nähe",
                "📅" to "Termine online buchen"
            )
        ),
        OnboardingPage(
            emoji     = "🩺",
            title     = "Symptome eingeben",
            subtitle  = "Wir finden den passenden Arzt",
            description = "Wählen Sie Ihre Symptome auf der Körperkarte oder aus der vollständigen Liste. IDAS empfiehlt den besten Facharzt für Sie.",
            gradient  = listOf(Color(0xFF2E7D32), Color(0xFF43A047)),
            features  = listOf(
                "🗺️" to "Interaktive Körperkarte",
                "⭐" to "KI-basierte Empfehlungen",
                "🥇" to "Top-Ärzte nach Punktzahl"
            )
        ),
        OnboardingPage(
            emoji     = "📱",
            title     = "Alles auf einen Blick",
            subtitle  = "Ihr Gesundheits-Dashboard",
            description = "Verwalten Sie Ihre Termine, sehen Sie Ihren nächsten Arztbesuch im Countdown und exportieren Sie Ihre Daten als PDF.",
            gradient  = listOf(Color(0xFF6A1B9A), Color(0xFF8E24AA)),
            features  = listOf(
                "📄" to "PDF Export & QR-Code",
                "🔔" to "Terminerinnerungen",
                "🌍" to "Deutsch & Englisch"
            )
        )
    )

    val currentPage = pages[page]

    Box(modifier = Modifier.fillMaxSize()
        .background(Brush.verticalGradient(currentPage.gradient))) {

        Column(modifier = Modifier.fillMaxSize()) {

            // Skip button
            Row(modifier = Modifier.fillMaxWidth().padding(16.dp),
                horizontalArrangement = Arrangement.End) {
                if (page < pages.size - 1) {
                    TextButton(onClick = onFinish) {
                        Text("Überspringen", color = Color.White.copy(0.7f), fontSize = 14.sp)
                    }
                }
            }

            // Main content
            Column(
                modifier = Modifier.weight(1f).padding(horizontal = 32.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                // Animated emoji
                val scale by animateFloatAsState(
                    targetValue = 1f,
                    animationSpec = spring(Spring.DampingRatioMediumBouncy),
                    label = "emoji_scale"
                )

                Box(
                    modifier = Modifier.size(120.dp).clip(RoundedCornerShape(36.dp))
                        .background(Color.White.copy(0.2f)),
                    contentAlignment = Alignment.Center
                ) { Text(currentPage.emoji, fontSize = 64.sp) }

                Spacer(Modifier.height(32.dp))

                Text(currentPage.title, fontSize = 28.sp, fontWeight = FontWeight.Bold,
                    color = Color.White, textAlign = TextAlign.Center)
                Spacer(Modifier.height(8.dp))
                Text(currentPage.subtitle, fontSize = 16.sp,
                    color = Color.White.copy(0.85f), textAlign = TextAlign.Center)
                Spacer(Modifier.height(16.dp))
                Text(currentPage.description, fontSize = 14.sp,
                    color = Color.White.copy(0.75f), textAlign = TextAlign.Center,
                    lineHeight = 22.sp)

                Spacer(Modifier.height(32.dp))

                // Feature chips
                Column(verticalArrangement = Arrangement.spacedBy(10.dp),
                    horizontalAlignment = Alignment.CenterHorizontally) {
                    currentPage.features.forEach { (emoji, text) ->
                        Surface(color = Color.White.copy(0.2f),
                            shape = RoundedCornerShape(20.dp)) {
                            Row(modifier = Modifier.padding(horizontal = 20.dp, vertical = 10.dp),
                                verticalAlignment = Alignment.CenterVertically) {
                                Text(emoji, fontSize = 18.sp)
                                Spacer(Modifier.width(10.dp))
                                Text(text, fontSize = 14.sp, color = Color.White,
                                    fontWeight = FontWeight.Medium)
                            }
                        }
                    }
                }
            }

            // Bottom navigation
            Column(modifier = Modifier.padding(horizontal = 32.dp, vertical = 32.dp),
                horizontalAlignment = Alignment.CenterHorizontally) {

                // Page dots
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp),
                    modifier = Modifier.padding(bottom = 24.dp)) {
                    pages.indices.forEach { i ->
                        Box(modifier = Modifier
                            .height(8.dp)
                            .width(if (i == page) 24.dp else 8.dp)
                            .clip(CircleShape)
                            .background(if (i == page) Color.White else Color.White.copy(0.4f))
                        )
                    }
                }

                // Next / Start button
                Button(
                    onClick = {
                        if (page < pages.size - 1) page++
                        else onFinish()
                    },
                    modifier = Modifier.fillMaxWidth().height(54.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Color.White),
                    shape = RoundedCornerShape(16.dp)
                ) {
                    Text(
                        when (page) {
                            0 -> "Los geht's →"
                            1 -> "Weiter →"
                            else -> "App starten 🚀"
                        },
                        color = currentPage.gradient[0],
                        fontSize = 16.sp, fontWeight = FontWeight.Bold
                    )
                }
            }
        }
    }
}
