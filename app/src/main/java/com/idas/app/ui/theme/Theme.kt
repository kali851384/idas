package com.idas.app.ui.theme

import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.CompositionLocalProvider
import androidx.compose.ui.graphics.Color

private val IDASLightColorScheme = lightColorScheme(
    primary             = IDASBlue,
    onPrimary           = Color.White,
    primaryContainer    = IDASBlueLight,
    onPrimaryContainer  = IDASBlueDark,
    secondary           = IDASGreen,
    onSecondary         = Color.White,
    secondaryContainer  = Color(0xFFD4F5E9),
    background          = Color(0xFFF4F7FB),
    onBackground        = Color(0xFF0D1B2A),
    surface             = Color(0xFFFFFFFF),
    onSurface           = Color(0xFF0D1B2A),
    surfaceVariant      = Color(0xFFEEF4FF),
    onSurfaceVariant    = Color(0xFF6B7A8D),
    outline             = Color(0xFFE2E8F0),
    error               = IDASRed,
    onError             = Color.White,
)

private val IDASDarkColorScheme = darkColorScheme(
    primary             = Color(0xFF4A9FE8),
    onPrimary           = Color.White,
    primaryContainer    = Color(0xFF0D47A1),
    onPrimaryContainer  = Color(0xFFD6EAF8),
    secondary           = Color(0xFF00E096),
    onSecondary         = Color.Black,
    secondaryContainer  = Color(0xFF00875A),
    background          = Color(0xFF0D1117),
    onBackground        = Color(0xFFE8EDF2),
    surface             = Color(0xFF161B22),
    onSurface           = Color(0xFFE8EDF2),
    surfaceVariant      = Color(0xFF1C2128),
    onSurfaceVariant    = Color(0xFF8B949E),
    outline             = Color(0xFF30363D),
    error               = Color(0xFFFF6B6B),
    onError             = Color.White,
)

@Composable
fun IDASTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    content: @Composable () -> Unit
) {
    val colorScheme = if (darkTheme) IDASDarkColorScheme else IDASLightColorScheme
    CompositionLocalProvider(LocalDarkMode provides darkTheme) {
        MaterialTheme(
            colorScheme = colorScheme,
            typography  = Typography,
            content     = content
        )
    }
}