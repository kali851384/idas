package com.idas.app.screens

import androidx.compose.animation.core.*
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Text
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.alpha
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.scale
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.ui.theme.*
import kotlinx.coroutines.delay

@Composable
fun SplashScreen(onFinished: () -> Unit) {

    // Animations
    val logoScale = remember { Animatable(0.3f) }
    val logoAlpha = remember { Animatable(0f) }
    val textAlpha = remember { Animatable(0f) }
    val taglineAlpha = remember { Animatable(0f) }
    val dotScale1 = remember { Animatable(0.6f) }
    val dotScale2 = remember { Animatable(0.6f) }
    val dotScale3 = remember { Animatable(0.6f) }

    LaunchedEffect(Unit) {
        // Logo + text + tagline all fast
        logoScale.animateTo(1f, animationSpec = spring(
            dampingRatio = Spring.DampingRatioMediumBouncy,
            stiffness = Spring.StiffnessHigh
        ))
        logoAlpha.animateTo(1f, animationSpec = tween(150))
        textAlpha.animateTo(1f, animationSpec = tween(200))
        taglineAlpha.animateTo(1f, animationSpec = tween(200))

        // Just one quick dot loop
        dotScale1.animateTo(1f, animationSpec = tween(120))
        dotScale1.animateTo(0.6f, animationSpec = tween(120))
        dotScale2.animateTo(1f, animationSpec = tween(120))
        dotScale2.animateTo(0.6f, animationSpec = tween(120))
        dotScale3.animateTo(1f, animationSpec = tween(120))
        dotScale3.animateTo(0.6f, animationSpec = tween(120))

        delay(200)
        onFinished()
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(
                    listOf(IDASBlueDark, IDASBlue, Color(0xFF4A9FE8))
                )
            ),
        contentAlignment = Alignment.Center
    ) {
        Column(
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            // Logo box
            Box(
                modifier = Modifier
                    .scale(logoScale.value)
                    .alpha(logoAlpha.value)
                    .size(110.dp)
                    .clip(RoundedCornerShape(32.dp))
                    .background(Color.White.copy(alpha = 0.2f)),
                contentAlignment = Alignment.Center
            ) {
                Text("🏥", fontSize = 60.sp)
            }

            Spacer(Modifier.height(28.dp))

            // App name
            Text(
                "IDAS",
                fontSize = 44.sp,
                fontWeight = FontWeight.Bold,
                color = Color.White,
                letterSpacing = 6.sp,
                modifier = Modifier.alpha(textAlpha.value)
            )

            Spacer(Modifier.height(8.dp))

            // Tagline
            Text(
                "Gesundheitsportal Hannover",
                fontSize = 14.sp,
                color = Color.White.copy(0.8f),
                letterSpacing = 1.sp,
                modifier = Modifier.alpha(taglineAlpha.value)
            )

            Spacer(Modifier.height(60.dp))

            // Loading dots
            Row(
                horizontalArrangement = Arrangement.spacedBy(10.dp),
                verticalAlignment = Alignment.CenterVertically,
                modifier = Modifier.alpha(taglineAlpha.value)
            ) {
                Box(modifier = Modifier
                    .scale(dotScale1.value)
                    .size(10.dp)
                    .clip(RoundedCornerShape(5.dp))
                    .background(Color.White.copy(0.9f)))
                Box(modifier = Modifier
                    .scale(dotScale2.value)
                    .size(10.dp)
                    .clip(RoundedCornerShape(5.dp))
                    .background(Color.White.copy(0.9f)))
                Box(modifier = Modifier
                    .scale(dotScale3.value)
                    .size(10.dp)
                    .clip(RoundedCornerShape(5.dp))
                    .background(Color.White.copy(0.9f)))
            }
        }

        // Version text at bottom
        Text(
            "v1.0",
            fontSize = 11.sp,
            color = Color.White.copy(0.4f),
            modifier = Modifier
                .align(Alignment.BottomCenter)
                .padding(bottom = 32.dp)
                .alpha(taglineAlpha.value)
        )
    }
}
