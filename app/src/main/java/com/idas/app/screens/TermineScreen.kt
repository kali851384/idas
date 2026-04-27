package com.idas.app.screens

import androidx.compose.foundation.background
import androidx.compose.material.ExperimentalMaterialApi
import androidx.compose.material.pullrefresh.PullRefreshIndicator
import androidx.compose.material.pullrefresh.pullRefresh
import androidx.compose.material.pullrefresh.rememberPullRefreshState
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import android.app.Activity
import androidx.compose.ui.platform.LocalContext
import com.idas.app.models.BookingConfirmData
import com.idas.app.utils.PdfExportHelper
import com.idas.app.models.Termin
import com.idas.app.network.ApiService
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings
import kotlinx.coroutines.launch
import androidx.compose.foundation.rememberScrollState
import androidx.compose.runtime.rememberCoroutineScope

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TermineScreen(
    token: String,
    patientName: String = "",
    activity: android.app.Activity? = null,
    onBack: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val scope   = rememberCoroutineScope()
    val context = androidx.compose.ui.platform.LocalContext.current
    var termine by remember { mutableStateOf<List<Termin>>(emptyList()) }
    var loading     by remember { mutableStateOf(true) }
    var refreshing  by remember { mutableStateOf(false) }
    var error   by remember { mutableStateOf("") }
    var selectedQRTermin by remember { mutableStateOf<BookingConfirmData?>(null) }

    fun load(isRefresh: Boolean = false) {
        scope.launch {
            if (isRefresh) refreshing = true else loading = true
            try {
                val res = ApiService.getTermine(token)
                if (res.getBoolean("success")) {
                    val arr = res.getJSONArray("data")
                    termine = (0 until arr.length()).map {
                        val obj = arr.getJSONObject(it)
                        Termin(
                            terminId     = obj.getInt("termin_id"),
                            datum        = obj.getString("datum"),
                            beschreibung = obj.optString("beschreibung", ""),
                            arztName     = obj.getString("arzt_name"),
                            arztTelefon  = obj.optString("arzt_telefon", ""),
                            arztEmail    = obj.optString("arzt_email", ""),
                            fachbereich  = obj.getString("fachbereich"),
                            status       = obj.getString("status"),
                            symptome     = run {
                                val arr = obj.optJSONArray("symptome")
                                if (arr != null) (0 until arr.length()).map { arr.getString(it) } else emptyList()
                            }
                        )
                    }
                } else error = "Termine konnten nicht geladen werden."
            } catch (e: Exception) { error = Strings.get("error_server") }
            finally { loading = false; refreshing = false }
        }
    }

    LaunchedEffect(Unit) { load() }

    // Show QR screen overlay
    if (selectedQRTermin != null) {
        QRScreen(
            termin = selectedQRTermin,
            onBack = { selectedQRTermin = null }
        )
        return
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(Strings.get("termine_title")) },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.Default.ArrowBack, Strings.get("back"))
                    }
                },
                actions = {
                    if (termine.isNotEmpty()) {
                        IconButton(onClick = {
                            PdfExportHelper.exportAppointmentHistory(
                                context, termine, patientName.ifBlank { "Patient" }
                            )
                        }) {
                            Icon(Icons.Default.PictureAsPdf, null, tint = Color.White)
                        }
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
        when {
            loading -> Box(Modifier.fillMaxSize().padding(padding), Alignment.Center) {
                CircularProgressIndicator(color = IDASBlue)
            }
            error.isNotEmpty() -> Box(Modifier.fillMaxSize().padding(padding), Alignment.Center) {
                Text(error, color = IDASRed)
            }
            termine.isEmpty() -> Box(
                Modifier.fillMaxSize().background(bg).padding(padding),
                Alignment.Center
            ) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Text("📅", fontSize = 56.sp)
                    Spacer(Modifier.height(16.dp))
                    Text(Strings.get("termine_empty"),
                        fontWeight = FontWeight.Bold, fontSize = 16.sp, color = textPri)
                    Text(Strings.get("termine_empty_sub"),
                        color = textSec, fontSize = 13.sp,
                        modifier = Modifier.padding(top = 4.dp))
                }
            }
            else -> {
                val upcoming = termine.filter { it.status == "Bevorstehend" }
                val past     = termine.filter { it.status != "Bevorstehend" }
                LazyColumn(
                    modifier = Modifier.fillMaxSize().background(bg).padding(padding),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    if (upcoming.isNotEmpty()) {
                        item {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Box(modifier = Modifier.size(8.dp)
                                    .clip(RoundedCornerShape(4.dp))
                                    .background(IDASGreen))
                                Spacer(Modifier.width(8.dp))
                                Text("${Strings.get("termine_upcoming")} (${upcoming.size})",
                                    fontSize = 13.sp, fontWeight = FontWeight.Bold,
                                    color = textSec)
                            }
                        }
                        items(upcoming) { t ->
                            TerminCard(t, true, token, patientName,
                                onRefresh = { load() },
                                onQRCode = {
                                    selectedQRTermin = BookingConfirmData(
                                        terminId     = t.terminId,
                                        arztName     = t.arztName,
                                        fachbereich  = t.fachbereich,
                                        datum        = t.datum,
                                        patientName  = "",
                                        beschreibung = t.beschreibung
                                    )
                                }
                            )
                        }
                    }
                    if (past.isNotEmpty()) {
                        item {
                            Spacer(Modifier.height(4.dp))
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Box(modifier = Modifier.size(8.dp)
                                    .clip(RoundedCornerShape(4.dp))
                                    .background(IDASTextSecondary))
                                Spacer(Modifier.width(8.dp))
                                Text("${Strings.get("termine_past")} (${past.size})",
                                    fontSize = 13.sp, fontWeight = FontWeight.Bold,
                                    color = textSec)
                            }
                        }
                        items(past) { t ->
                            TerminCard(t, false, token, patientName,
                                onRefresh = { load() },
                                onQRCode = {
                                    selectedQRTermin = BookingConfirmData(
                                        terminId     = t.terminId,
                                        arztName     = t.arztName,
                                        fachbereich  = t.fachbereich,
                                        datum        = t.datum,
                                        patientName  = "",
                                        beschreibung = t.beschreibung
                                    )
                                }
                            )
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun TerminCard(
    termin: Termin,
    canCancel: Boolean,
    token: String,
    patientName: String = "",
    onRefresh: () -> Unit,
    onQRCode: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val context = androidx.compose.ui.platform.LocalContext.current
    val scope = rememberCoroutineScope()
    var showConfirm by remember { mutableStateOf(false) }

    if (showConfirm) {
        AlertDialog(
            onDismissRequest = { showConfirm = false },
            title = { Text(Strings.get("termine_cancel")) },
            text  = { Text(Strings.get("termine_cancel_q")) },
            confirmButton = {
                TextButton(onClick = {
                    showConfirm = false
                    scope.launch {
                        ApiService.cancelTermin(token, termin.terminId)
                        onRefresh()
                    }
                }) { Text(Strings.get("termine_cancel_yes"), color = IDASRed) }
            },
            dismissButton = {
                TextButton(onClick = { showConfirm = false }) {
                    Text(Strings.get("cancel"))
                }
            }
        )
    }

    Card(
        shape = RoundedCornerShape(18.dp),
        elevation = CardDefaults.cardElevation(2.dp),
        colors = CardDefaults.cardColors(containerColor = cardColor),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column {
            // Top color bar
            Box(
                modifier = Modifier.fillMaxWidth().height(4.dp)
                    .background(if (canCancel) IDASGreen else IDASBorder)
            )
            Column(modifier = Modifier.padding(16.dp)) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Box(
                        modifier = Modifier.size(44.dp).clip(RoundedCornerShape(12.dp))
                            .background(if (canCancel) Color(0xFFE8F5E9) else IDASBlueGray),
                        contentAlignment = Alignment.Center
                    ) { Text(if (canCancel) "📅" else "✓", fontSize = 20.sp) }
                    Spacer(Modifier.width(12.dp))
                    Column(modifier = Modifier.weight(1f)) {
                        Text(termin.arztName, fontWeight = FontWeight.Bold,
                            fontSize = 15.sp, color = textPri)
                        Text(termin.fachbereich, fontSize = 12.sp,
                            color = if (canCancel) IDASGreen else IDASTextSecondary)
                    }
                    Surface(
                        color = if (canCancel) Color(0xFFE8F5E9) else IDASBlueGray,
                        shape = RoundedCornerShape(8.dp)
                    ) {
                        Text(
                            if (canCancel) Strings.get("termine_status_up")
                            else Strings.get("termine_status_done"),
                            modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp),
                            fontSize = 11.sp, fontWeight = FontWeight.Bold,
                            color = if (canCancel) IDASGreenDark else IDASTextSecondary
                        )
                    }
                }

                Divider(modifier = Modifier.padding(vertical = 12.dp), color = border)

                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(Icons.Default.Schedule, null,
                        tint = textSec, modifier = Modifier.size(15.dp))
                    Text(" ${termin.datum}", fontSize = 13.sp, color = textSec)
                }
                if (termin.arztTelefon.isNotBlank()) {
                    Spacer(Modifier.height(4.dp))
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(Icons.Default.Phone, null,
                            tint = textSec, modifier = Modifier.size(15.dp))
                        Text(" ${termin.arztTelefon}", fontSize = 13.sp, color = textSec)
                    }
                }
                if (termin.beschreibung.isNotBlank()) {
                    Spacer(Modifier.height(6.dp))
                    Surface(color = IDASBackground, shape = RoundedCornerShape(8.dp),
                        modifier = Modifier.fillMaxWidth()) {
                        Text(termin.beschreibung,
                            modifier = Modifier.padding(8.dp),
                            fontSize = 12.sp, color = textSec, maxLines = 2)
                    }
                }

                // Show symptoms
                if (termin.symptome.isNotEmpty()) {
                    Spacer(Modifier.height(8.dp))
                    Text(
                        if (Strings.language == "de") "🔍 Symptome:" else "🔍 Symptoms:",
                        fontSize = 11.sp, color = textSec, fontWeight = FontWeight.Bold
                    )
                    Spacer(Modifier.height(4.dp))
                    termin.symptome.chunked(2).forEach { rowItems ->
                        Row(horizontalArrangement = Arrangement.spacedBy(4.dp),
                            modifier = Modifier.padding(bottom = 4.dp)) {
                            rowItems.forEach { symptom ->
                                Surface(color = IDASBlueLight, shape = RoundedCornerShape(20.dp)) {
                                    Text(symptom,
                                        modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp),
                                        fontSize = 11.sp, color = IDASBlueDark, maxLines = 1)
                                }
                            }
                        }
                    }
                }

                Spacer(Modifier.height(12.dp))

                // Action buttons row
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    // QR Code button
                    OutlinedButton(
                        onClick = onQRCode,
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFF6A1B9A)),
                        shape = RoundedCornerShape(10.dp),
                        contentPadding = PaddingValues(horizontal = 12.dp, vertical = 6.dp),
                        modifier = Modifier.height(36.dp)
                    ) {
                        Text("🔲 QR-Code", fontSize = 12.sp, fontWeight = FontWeight.Bold)
                    }

                    // PDF export button
                    OutlinedButton(
                        onClick = {
                            com.idas.app.utils.PdfExportHelper.exportAppointmentHistory(
                                context, listOf(termin), patientName.ifBlank { "Patient" }
                            )
                        },
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = Color(0xFFE65100)),
                        shape = RoundedCornerShape(10.dp),
                        contentPadding = PaddingValues(horizontal = 12.dp, vertical = 6.dp),
                        modifier = Modifier.height(36.dp)
                    ) {
                        Icon(Icons.Default.PictureAsPdf, null, modifier = Modifier.size(14.dp))
                        Spacer(Modifier.width(4.dp))
                        Text("PDF", fontSize = 12.sp, fontWeight = FontWeight.Bold)
                    }

                    // Cancel button - only for upcoming
                    if (canCancel) {
                        OutlinedButton(
                            onClick = { showConfirm = true },
                            colors = ButtonDefaults.outlinedButtonColors(contentColor = IDASRed),
                            shape = RoundedCornerShape(10.dp),
                            contentPadding = PaddingValues(horizontal = 12.dp, vertical = 6.dp),
                            modifier = Modifier.height(36.dp)
                        ) {
                            Icon(Icons.Default.Close, null, modifier = Modifier.size(14.dp))
                            Spacer(Modifier.width(4.dp))
                            Text(Strings.get("termine_cancel"), fontSize = 12.sp)
                        }
                    }
                }
            }
        }
    }
}
