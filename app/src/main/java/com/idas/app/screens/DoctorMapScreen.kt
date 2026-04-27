package com.idas.app.screens

import android.content.Intent
import android.net.Uri
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.models.Arzt
import com.idas.app.models.FachbereichResult
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DoctorMapScreen(
    results: List<FachbereichResult>,
    onBack: () -> Unit,
    onBook: (arztId: Int, arztName: String, fachbereich: String) -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val context    = LocalContext.current
    val allDoctors = results.flatMap { fb -> fb.aerzte.map { Pair(it, fb.fachbereich) } }
    val withAddress = allDoctors.filter { it.first.addresse.isNotBlank() }

    // Build Google Maps URL with multiple search results
    fun openAllOnGoogleMaps() {
        if (withAddress.isEmpty()) return
        // Use Google Maps search URL — shows pins for all addresses
        val query = withAddress.take(10).joinToString("|") {
            "${it.first.name}, ${it.first.addresse}, Hannover"
        }
        val uri = Uri.parse(
            "https://www.google.com/maps/search/${Uri.encode(
                withAddress.take(5).map { it.first.addresse + " Hannover" }.joinToString(" OR ")
            )}"
        )
        try {
            context.startActivity(Intent(Intent.ACTION_VIEW, uri).apply {
                setPackage("com.google.android.apps.maps")
            })
        } catch (e: Exception) {
            context.startActivity(Intent(Intent.ACTION_VIEW, uri))
        }
    }

    // Open single doctor
    fun openSingle(address: String, name: String) {
        val uri = Uri.parse("geo:0,0?q=${Uri.encode("$address, Hannover")}")
        try {
            context.startActivity(Intent(Intent.ACTION_VIEW, uri).apply {
                setPackage("com.google.android.apps.maps")
            })
        } catch (e: Exception) {
            context.startActivity(Intent(Intent.ACTION_VIEW, uri))
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text(if (Strings.language == "de") "Ärzte auf Karte" else "Doctors on Map")
                        Text("${allDoctors.size} ${if (Strings.language == "de") "Ärzte" else "doctors"}",
                            fontSize = 11.sp, color = Color.White.copy(0.8f))
                    }
                },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.Default.ArrowBack, Strings.get("back"))
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = IDASBlue,
                    titleContentColor = Color.White,
                    navigationIconContentColor = Color.White
                )
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(bg)
                .padding(padding)
        ) {
            // Open all in maps button
            if (withAddress.isNotEmpty()) {
                Button(
                    onClick = { openAllOnGoogleMaps() },
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(16.dp)
                        .height(50.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = IDASBlue),
                    shape = RoundedCornerShape(14.dp)
                ) {
                    Icon(Icons.Default.Map, null, modifier = Modifier.size(20.dp))
                    Spacer(Modifier.width(10.dp))
                    Text(
                        if (Strings.language == "de")
                            "Alle ${withAddress.size} Ärzte in Google Maps öffnen"
                        else "Open all ${withAddress.size} doctors in Google Maps",
                        fontSize = 14.sp, fontWeight = FontWeight.Bold
                    )
                }
            }

            // Doctor list grouped by Fachbereich
            LazyColumn(
                contentPadding = PaddingValues(horizontal = 16.dp, vertical = 4.dp),
                verticalArrangement = Arrangement.spacedBy(10.dp)
            ) {
                results.forEachIndexed { fbIdx, fb ->
                    val color = listOf(
                        Color(0xFF1565C0), Color(0xFF2E7D32), Color(0xFF6A1B9A)
                    ).getOrElse(fbIdx) { IDASBlue }

                    item {
                        Surface(
                            color = color.copy(0.1f),
                            shape = RoundedCornerShape(12.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Row(
                                modifier = Modifier.padding(12.dp),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Text("🏥", fontSize = 20.sp)
                                Spacer(Modifier.width(10.dp))
                                Column(modifier = Modifier.weight(1f)) {
                                    Text(fb.fachbereich, fontWeight = FontWeight.Bold,
                                        fontSize = 14.sp, color = color)
                                    Text("${fb.aerzte.size} ${if (Strings.language == "de") "Ärzte" else "doctors"}",
                                        fontSize = 11.sp, color = textSec)
                                }
                                Icon(Icons.Default.Star, null, tint = color,
                                    modifier = Modifier.size(14.dp))
                                Text(" ${fb.punkte}", fontSize = 12.sp, color = color,
                                    fontWeight = FontWeight.Bold)
                            }
                        }
                    }

                    items(fb.aerzte.size) { idx ->
                        val arzt = fb.aerzte[idx]
                        MapDoctorCard(
                            arzt        = arzt,
                            fachbereich = fb.fachbereich,
                            accentColor = color,
                            onOpenMap   = { openSingle(arzt.addresse, arzt.name) },
                            onCall      = {
                                if (arzt.telefon.isNotBlank())
                                    context.startActivity(Intent(Intent.ACTION_DIAL,
                                        Uri.parse("tel:${arzt.telefon}")))
                            },
                            onBook      = { onBook(arzt.arztId, arzt.name, fb.fachbereich) }
                        )
                    }
                }

                item { Spacer(Modifier.height(16.dp)) }
            }
        }
    }
}

@Composable
fun MapDoctorCard(
    arzt: Arzt,
    fachbereich: String,
    accentColor: Color = IDASBlue,
    onOpenMap: () -> Unit,
    onCall: () -> Unit,
    onBook: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    Card(
        shape = RoundedCornerShape(16.dp),
        elevation = CardDefaults.cardElevation(2.dp),
        colors = CardDefaults.cardColors(containerColor = cardColor),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Box(
                    modifier = Modifier.size(44.dp).clip(RoundedCornerShape(12.dp))
                        .background(accentColor.copy(0.12f)),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        arzt.name.split(" ").mapNotNull { it.firstOrNull()?.uppercaseChar() }
                            .take(2).joinToString(""),
                        fontWeight = FontWeight.Bold, color = accentColor, fontSize = 14.sp
                    )
                }
                Spacer(Modifier.width(12.dp))
                Column(modifier = Modifier.weight(1f)) {
                    Text(arzt.name, fontWeight = FontWeight.Bold,
                        fontSize = 14.sp, color = textPri)
                    Text(fachbereich, fontSize = 12.sp, color = textSec)
                    if (arzt.telefon.isNotBlank()) {
                        Text(arzt.telefon, fontSize = 11.sp, color = textSec)
                    }
                }
            }

            if (arzt.addresse.isNotBlank()) {
                Spacer(Modifier.height(8.dp))
                Surface(
                    onClick = onOpenMap,
                    color = IDASBackground,
                    shape = RoundedCornerShape(10.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Row(
                        modifier = Modifier.padding(10.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Icon(Icons.Default.LocationOn, null, tint = accentColor,
                            modifier = Modifier.size(16.dp))
                        Spacer(Modifier.width(6.dp))
                        Text(arzt.addresse, fontSize = 12.sp, color = textPri,
                            modifier = Modifier.weight(1f))
                        Surface(
                            color = accentColor.copy(0.1f),
                            shape = RoundedCornerShape(6.dp)
                        ) {
                            Text(
                                if (Strings.language == "de") "Karte" else "Map",
                                modifier = Modifier.padding(horizontal = 6.dp, vertical = 3.dp),
                                fontSize = 10.sp, color = accentColor, fontWeight = FontWeight.Bold
                            )
                        }
                    }
                }
            }

            Spacer(Modifier.height(10.dp))

            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                if (arzt.telefon.isNotBlank()) {
                    OutlinedButton(
                        onClick = onCall,
                        modifier = Modifier.height(36.dp),
                        shape = RoundedCornerShape(10.dp),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = IDASGreen),
                        contentPadding = PaddingValues(horizontal = 12.dp)
                    ) {
                        Icon(Icons.Default.Phone, null, modifier = Modifier.size(14.dp))
                        Spacer(Modifier.width(4.dp))
                        Text(if (Strings.language == "de") "Anrufen" else "Call",
                            fontSize = 12.sp, fontWeight = FontWeight.Bold)
                    }
                }
                if (arzt.addresse.isNotBlank()) {
                    OutlinedButton(
                        onClick = onOpenMap,
                        modifier = Modifier.height(36.dp),
                        shape = RoundedCornerShape(10.dp),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = accentColor),
                        contentPadding = PaddingValues(horizontal = 12.dp)
                    ) {
                        Icon(Icons.Default.Map, null, modifier = Modifier.size(14.dp))
                        Spacer(Modifier.width(4.dp))
                        Text(if (Strings.language == "de") "Karte" else "Map",
                            fontSize = 12.sp, fontWeight = FontWeight.Bold)
                    }
                }
                Button(
                    onClick = onBook,
                    modifier = Modifier.weight(1f).height(36.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = accentColor),
                    shape = RoundedCornerShape(10.dp),
                    contentPadding = PaddingValues(horizontal = 12.dp)
                ) {
                    Text(Strings.get("matching_book"),
                        fontSize = 12.sp, fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}
