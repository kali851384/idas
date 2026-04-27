package com.idas.app.ui.theme

import androidx.compose.runtime.Composable
import androidx.compose.runtime.staticCompositionLocalOf
import androidx.compose.ui.graphics.Color

// Static brand colors (never change)
val IDASBlue          = Color(0xFF1E6FD9)
val IDASBlueDark      = Color(0xFF0D47A1)
val IDASBlueLight     = Color(0xFFE3F0FF)
val IDASGreen         = Color(0xFF00C17C)
val IDASGreenDark     = Color(0xFF00875A)
val IDASRed           = Color(0xFFE53935)

// Composition local for dark mode flag
val LocalDarkMode = staticCompositionLocalOf { false }

// Dynamic semantic colors (use these everywhere)
val IDASBackground: Color
    @Composable get() = if (LocalDarkMode.current) Color(0xFF0D1117) else Color(0xFFF4F7FB)

val IDASCard: Color
    @Composable get() = if (LocalDarkMode.current) Color(0xFF161B22) else Color(0xFFFFFFFF)

val IDASTextPrimary: Color
    @Composable get() = if (LocalDarkMode.current) Color(0xFFE8EDF2) else Color(0xFF0D1B2A)

val IDASTextSecondary: Color
    @Composable get() = if (LocalDarkMode.current) Color(0xFF8B949E) else Color(0xFF6B7A8D)

val IDASBorder: Color
    @Composable get() = if (LocalDarkMode.current) Color(0xFF30363D) else Color(0xFFE2E8F0)

val IDASBlueGray: Color
    @Composable get() = if (LocalDarkMode.current) Color(0xFF1C2128) else Color(0xFFEEF4FF)

val IDASSurface: Color
    @Composable get() = if (LocalDarkMode.current) Color(0xFF161B22) else Color(0xFFFFFFFF)

// Keep these for backward compat
val Purple80 = Color(0xFFD0BCFF)
val PurpleGrey80 = Color(0xFFCCC2DC)
val Pink80 = Color(0xFFEFB8C8)
val Purple40 = Color(0xFF6650A4)
val PurpleGrey40 = Color(0xFF625B71)
val Pink40 = Color(0xFF7D5260)