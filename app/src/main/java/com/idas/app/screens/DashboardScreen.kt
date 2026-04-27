package com.idas.app.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.res.painterResource
import com.idas.app.R
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.models.Termin
import com.idas.app.network.ApiService
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings
import java.text.SimpleDateFormat
import java.util.*

fun getDaysUntil(datumString: String): Long {
    return try {
        val sdf = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
        val date = sdf.parse(datumString) ?: return -1
        val diff = date.time - System.currentTimeMillis()
        if (diff < 0) -1L else diff / (1000 * 60 * 60 * 24)
    } catch (e: Exception) { -1 }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DashboardScreen(
    token: String,
    patientName: String,
    currentLang: String,
    darkMode: Boolean,
    onDarkModeToggle: (Boolean) -> Unit,
    onSymptome:  () -> Unit,
    onTermine:   () -> Unit,
    onProfil:    () -> Unit,
    onSupport:   () -> Unit,
    onLanguage:  (String) -> Unit,
    onLogout:    () -> Unit
) {
    var showLangMenu by remember { mutableStateOf(false) }
    var nextTermin   by remember { mutableStateOf<Termin?>(null) }

    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    LaunchedEffect(Unit) {
        try {
            val res = ApiService.getTermine(token)
            if (res.getBoolean("success")) {
                val arr = res.getJSONArray("data")
                val upcoming = (0 until arr.length()).map {
                    val o = arr.getJSONObject(it)
                    Termin(o.getInt("termin_id"), o.getString("datum"),
                        o.optString("beschreibung",""), o.getString("arzt_name"),
                        o.optString("arzt_telefon",""), o.optString("arzt_email",""),
                        o.getString("fachbereich"), o.getString("status"))
                }.filter { it.status == "Bevorstehend" }.sortedBy { it.datum }
                nextTermin = upcoming.firstOrNull()
            }
        } catch (e: Exception) { }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Text("IDAS", fontWeight = FontWeight.Bold, fontSize = 20.sp, color = Color.White)
                },
                actions = {
                    Box {
                        IconButton(onClick = { showLangMenu = true }) {
                            Text(if (currentLang == "de") "🇩🇪" else "🇬🇧", fontSize = 20.sp)
                        }
                        DropdownMenu(expanded = showLangMenu,
                            onDismissRequest = { showLangMenu = false }) {
                            DropdownMenuItem(text = { Text("🇩🇪  Deutsch") },
                                onClick = { onLanguage("de"); showLangMenu = false })
                            DropdownMenuItem(text = { Text("🇬🇧  English") },
                                onClick = { onLanguage("en"); showLangMenu = false })
                        }
                    }
                    IconButton(onClick = { onDarkModeToggle(!darkMode) }) {
                        Icon(
                            if (darkMode) Icons.Default.LightMode else Icons.Default.DarkMode,
                            contentDescription = if (darkMode) "Light mode" else "Dark mode",
                            tint = Color.White
                        )
                    }
                    IconButton(onClick = onProfil) {
                        Box(modifier = Modifier.size(36.dp).clip(CircleShape)
                            .background(Color.White.copy(0.2f)),
                            contentAlignment = Alignment.Center) {
                            Text(patientName.firstOrNull()?.uppercaseChar()?.toString() ?: "P",
                                color = Color.White, fontWeight = FontWeight.Bold, fontSize = 15.sp)
                        }
                    }
                    IconButton(onClick = onLogout) {
                        Icon(Icons.Default.ExitToApp, "Logout", tint = Color.White)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = IDASBlue, titleContentColor = Color.White)
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(bg)
                .padding(padding)
                .verticalScroll(rememberScrollState())
        ) {
            // Hero
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(Brush.verticalGradient(listOf(IDASBlueDark, IDASBlue)))
                    .padding(horizontal = 24.dp, vertical = 28.dp)
            ) {
                Column {
                    Text(
                        "${Strings.get("dashboard_hello")}, $patientName 👋",
                        fontSize = 26.sp, fontWeight = FontWeight.Bold, color = Color.White
                    )
                    Spacer(Modifier.height(4.dp))
                    Text(
                        Strings.get("dashboard_help"),
                        fontSize = 15.sp, color = Color.White.copy(0.85f)
                    )
                    Spacer(Modifier.height(20.dp))
                    Button(
                        onClick = onSymptome,
                        colors = ButtonDefaults.buttonColors(containerColor = Color.White),
                        shape = RoundedCornerShape(12.dp),
                        modifier = Modifier.height(46.dp)
                    ) {
                        Icon(Icons.Default.Search, null,
                            tint = IDASBlue, modifier = Modifier.size(18.dp))
                        Spacer(Modifier.width(8.dp))
                        Text(Strings.get("dashboard_find"),
                            color = IDASBlue, fontWeight = FontWeight.Bold, fontSize = 15.sp)
                    }
                }
            }

            Column(modifier = Modifier.padding(16.dp)) {

                // Countdown
                nextTermin?.let { termin ->
                    val days = getDaysUntil(termin.datum)
                    if (days >= 0) {
                        val bgColor: Color = when {
                            days == 0L -> if (darkMode) Color(0xFF1B3A2A) else Color(0xFFE8F5E9)
                            days == 1L -> if (darkMode) Color(0xFF3A2A00) else Color(0xFFFFF8E1)
                            else       -> cardColor
                        }
                        val accent: Color = when {
                            days == 0L -> IDASGreenDark
                            days == 1L -> Color(0xFFE65100)
                            else       -> IDASBlue
                        }
                        val emoji = when { days == 0L -> "📅"; days == 1L -> "⏰"; else -> "🗓" }
                        val label = when {
                            days == 0L -> if (currentLang == "de") "Heute!" else "Today!"
                            days == 1L -> if (currentLang == "de") "Morgen" else "Tomorrow"
                            else -> if (currentLang == "de") "In $days Tagen" else "In $days days"
                        }

                        Card(
                            modifier = Modifier.fillMaxWidth().clickable { onTermine() },
                            shape = RoundedCornerShape(20.dp),
                            colors = CardDefaults.cardColors(containerColor = bgColor),
                            elevation = CardDefaults.cardElevation(3.dp)
                        ) {
                            Row(modifier = Modifier.padding(18.dp),
                                verticalAlignment = Alignment.CenterVertically) {
                                Box(modifier = Modifier.size(56.dp)
                                    .clip(RoundedCornerShape(16.dp))
                                    .background(accent.copy(0.12f)),
                                    contentAlignment = Alignment.Center) {
                                    Text(emoji, fontSize = 28.sp)
                                }
                                Spacer(Modifier.width(16.dp))
                                Column(modifier = Modifier.weight(1f)) {
                                    Text(
                                        if (currentLang == "de") "Nächster Termin" else "Next Appointment",
                                        fontSize = 12.sp, color = accent, fontWeight = FontWeight.Bold)
                                    Text(termin.arztName, fontSize = 17.sp,
                                        fontWeight = FontWeight.Bold, color = textPri)
                                    Text(termin.fachbereich, fontSize = 13.sp, color = textSec)
                                    Text(termin.datum.take(16), fontSize = 12.sp,
                                        color = textSec, modifier = Modifier.padding(top = 2.dp))
                                }
                                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                    Text(if (days == 0L) "🎯" else days.toString(),
                                        fontSize = if (days == 0L) 26.sp else 30.sp,
                                        fontWeight = FontWeight.Bold, color = accent)
                                    Text(label, fontSize = 11.sp, color = accent,
                                        fontWeight = FontWeight.Bold)
                                }
                            }
                        }
                        Spacer(Modifier.height(20.dp))
                    }
                }

                // Section title
                Text(
                    if (currentLang == "de") "Was möchten Sie tun?" else "What would you like to do?",
                    fontSize = 16.sp, fontWeight = FontWeight.Bold,
                    color = textPri, modifier = Modifier.padding(bottom = 14.dp)
                )

                // Main action cards
                DashActionCard(
                    icon       = Icons.Default.Search,
                    emoji      = "🔍",
                    title      = Strings.get("dashboard_symptom"),
                    subtitle   = Strings.get("dashboard_symptom_sub"),
                    iconColor  = Color.White,
                    iconBg     = Brush.linearGradient(listOf(IDASBlue, Color(0xFF4A9FE8))),
                    cardColor  = cardColor,
                    textPri    = textPri,
                    textSec    = textSec,
                    borderCol  = border,
                    onClick    = onSymptome
                )
                Spacer(Modifier.height(12.dp))
                DashActionCard(
                    icon       = Icons.Default.DateRange,
                    emoji      = "📅",
                    title      = Strings.get("dashboard_termine"),
                    subtitle   = Strings.get("dashboard_termine_sub"),
                    iconColor  = Color.White,
                    iconBg     = Brush.linearGradient(listOf(IDASGreen, Color(0xFF00E096))),
                    cardColor  = cardColor,
                    textPri    = textPri,
                    textSec    = textSec,
                    borderCol  = border,
                    onClick    = onTermine
                )
                Spacer(Modifier.height(12.dp))
                DashActionCard(
                    icon       = Icons.Default.Person,
                    emoji      = "👤",
                    title      = Strings.get("dashboard_profil"),
                    subtitle   = Strings.get("dashboard_profil_sub"),
                    iconColor  = Color.White,
                    iconBg     = Brush.linearGradient(listOf(Color(0xFF7B1FA2), Color(0xFFAB47BC))),
                    cardColor  = cardColor,
                    textPri    = textPri,
                    textSec    = textSec,
                    borderCol  = border,
                    onClick    = onProfil
                )
                Spacer(Modifier.height(12.dp))
                DashActionCard(
                    icon       = Icons.Default.HeadsetMic,
                    emoji      = "🎧",
                    title      = Strings.get("support_title"),
                    subtitle   = if (currentLang == "de")
                        "Fragen oder Probleme? Wir helfen Ihnen."
                    else "Questions or problems? We help you.",
                    iconColor  = Color.White,
                    iconBg     = Brush.linearGradient(listOf(Color(0xFFE65100), Color(0xFFFF7043))),
                    cardColor  = cardColor,
                    textPri    = textPri,
                    textSec    = textSec,
                    borderCol  = border,
                    onClick    = onSupport
                )

                Spacer(Modifier.height(20.dp))

                //  Tip
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(16.dp),
                    colors = CardDefaults.cardColors(containerColor = blueGray),
                    elevation = CardDefaults.cardElevation(0.dp)
                ) {
                    Row(modifier = Modifier.padding(16.dp),
                        verticalAlignment = Alignment.CenterVertically) {
                        Text("💡", fontSize = 26.sp)
                        Spacer(Modifier.width(14.dp))
                        Column {
                            Text(Strings.get("dashboard_tip"),
                                fontWeight = FontWeight.Bold, fontSize = 14.sp, color = IDASBlue)
                            Text(Strings.get("dashboard_tip_text"),
                                fontSize = 13.sp, color = textSec,
                                modifier = Modifier.padding(top = 3.dp))
                        }
                    }
                }
            }
            Spacer(Modifier.height(24.dp))
        }
    }
}

@Composable
fun DashActionCard(
    icon: ImageVector,
    emoji: String,
    title: String,
    subtitle: String,
    iconColor: Color,
    iconBg: Brush,
    cardColor: Color,
    textPri: Color,
    textSec: Color,
    borderCol: Color,
    onClick: () -> Unit
) {
    Card(
        modifier = Modifier.fillMaxWidth().clickable(onClick = onClick),
        shape = RoundedCornerShape(18.dp),
        colors = CardDefaults.cardColors(containerColor = cardColor),
        elevation = CardDefaults.cardElevation(3.dp)
    ) {
        Row(
            modifier = Modifier.padding(18.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(60.dp)
                    .clip(RoundedCornerShape(18.dp))
                    .background(iconBg),
                contentAlignment = Alignment.Center
            ) {
                Text(emoji, fontSize = 28.sp)
            }
            Spacer(Modifier.width(18.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(title, fontWeight = FontWeight.Bold,
                    fontSize = 17.sp, color = textPri)
                Spacer(Modifier.height(3.dp))
                Text(subtitle, fontSize = 13.sp,
                    color = textSec, lineHeight = 18.sp)
            }
            Spacer(Modifier.width(8.dp))
            Icon(Icons.Default.ChevronRight, null,
                tint = borderCol, modifier = Modifier.size(26.dp))
        }
    }
}