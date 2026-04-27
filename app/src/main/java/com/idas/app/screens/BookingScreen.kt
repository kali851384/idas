package com.idas.app.screens

import android.app.DatePickerDialog
import android.widget.DatePicker
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.CalendarToday
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.models.BookingConfirmData
import com.idas.app.network.ApiService
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings
import kotlinx.coroutines.launch
import java.util.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun BookingScreen(
    token: String,
    patientId: Int,
    patientName: String,
    arztId: Int,
    arztName: String,
    fachbereich: String,
    symptomIds: String = "",
    onBack: () -> Unit,
    onBooked: (BookingConfirmData) -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val context      = LocalContext.current
    val scope        = rememberCoroutineScope()
    var datum        by remember { mutableStateOf("") }
    var uhrzeit      by remember { mutableStateOf("09:00") }
    var beschreibung by remember { mutableStateOf("") }
    var error        by remember { mutableStateOf("") }
    var loading      by remember { mutableStateOf(false) }

    val slots = listOf(
        "08:00","08:30","09:00","09:30","10:00","10:30",
        "11:00","11:30","13:00","13:30","14:00","14:30","15:00","15:30","16:00"
    )

    // Date picker dialog
    val calendar = Calendar.getInstance()
    val datePickerDialog = DatePickerDialog(
        context,
        { _: DatePicker, year: Int, month: Int, day: Int ->
            datum = "%04d-%02d-%02d".format(year, month + 1, day)
            error = ""
        },
        calendar.get(Calendar.YEAR),
        calendar.get(Calendar.MONTH),
        calendar.get(Calendar.DAY_OF_MONTH)
    ).apply {
        // Only allow future dates
        datePicker.minDate = System.currentTimeMillis() + (24 * 60 * 60 * 1000)
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(Strings.get("booking_title")) },
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
                .verticalScroll(rememberScrollState())
                .padding(16.dp)
        ) {
            // Doctor card
            Card(
                shape = RoundedCornerShape(20.dp),
                elevation = CardDefaults.cardElevation(3.dp),
                colors = CardDefaults.cardColors(containerColor = cardColor),
                modifier = Modifier.fillMaxWidth()
            ) {
                Box(modifier = Modifier.fillMaxWidth()
                    .background(Brush.horizontalGradient(listOf(IDASBlue, Color(0xFF4A9FE8))))
                    .padding(20.dp)) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Box(modifier = Modifier.size(52.dp).clip(RoundedCornerShape(16.dp))
                            .background(Color.White.copy(0.2f)),
                            contentAlignment = Alignment.Center) {
                            Text("🩺", fontSize = 24.sp)
                        }
                        Spacer(Modifier.width(14.dp))
                        Column {
                            Text(arztName, fontWeight = FontWeight.Bold,
                                fontSize = 16.sp, color = Color.White)
                            Surface(color = Color.White.copy(0.2f),
                                shape = RoundedCornerShape(8.dp),
                                modifier = Modifier.padding(top = 5.dp)) {
                                Text(fachbereich,
                                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp),
                                    fontSize = 12.sp, color = Color.White,
                                    fontWeight = FontWeight.Bold)
                            }
                        }
                    }
                }
            }

            Spacer(Modifier.height(16.dp))

            // Date picker card
            Card(
                shape = RoundedCornerShape(20.dp),
                elevation = CardDefaults.cardElevation(2.dp),
                colors = CardDefaults.cardColors(containerColor = cardColor),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Text(Strings.get("booking_date"), fontSize = 11.sp,
                        color = textSec, fontWeight = FontWeight.Bold)
                    Spacer(Modifier.height(10.dp))

                    // Tappable date box
                    Surface(
                        onClick = { datePickerDialog.show() },
                        color = if (datum.isBlank()) IDASBackground else IDASBlueLight,
                        shape = RoundedCornerShape(12.dp),
                        border = ButtonDefaults.outlinedButtonBorder,
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Row(
                            modifier = Modifier.padding(16.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Icon(Icons.Default.CalendarToday, null,
                                tint = IDASBlue, modifier = Modifier.size(22.dp))
                            Spacer(Modifier.width(12.dp))
                            Text(
                                if (datum.isBlank())
                                    if (Strings.language == "de") "Datum auswählen…" else "Select date…"
                                else datum,
                                fontSize = 15.sp,
                                fontWeight = if (datum.isBlank()) FontWeight.Normal else FontWeight.Bold,
                                color = if (datum.isBlank()) IDASTextSecondary else IDASBlueDark
                            )
                        }
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // Time slots
            Card(
                shape = RoundedCornerShape(20.dp),
                elevation = CardDefaults.cardElevation(2.dp),
                colors = CardDefaults.cardColors(containerColor = cardColor),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Text(Strings.get("booking_time"), fontSize = 11.sp,
                        color = textSec, fontWeight = FontWeight.Bold)
                    Spacer(Modifier.height(10.dp))
                    slots.chunked(3).forEach { row ->
                        Row(modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                            row.forEach { slot ->
                                val selected = uhrzeit == slot
                                Button(
                                    onClick = { uhrzeit = slot },
                                    modifier = Modifier.weight(1f).height(38.dp),
                                    shape = RoundedCornerShape(10.dp),
                                    colors = ButtonDefaults.buttonColors(
                                        containerColor = if (selected) IDASBlue else IDASBackground,
                                        contentColor   = if (selected) Color.White else IDASTextPrimary
                                    ),
                                    contentPadding = PaddingValues(0.dp),
                                    elevation = ButtonDefaults.buttonElevation(
                                        defaultElevation = if (selected) 3.dp else 0.dp)
                                ) {
                                    Text(slot, fontSize = 12.sp, fontWeight = FontWeight.Medium)
                                }
                            }
                            repeat(3 - row.size) { Spacer(Modifier.weight(1f)) }
                        }
                        Spacer(Modifier.height(6.dp))
                    }
                }
            }

            Spacer(Modifier.height(12.dp))

            // Description
            Card(
                shape = RoundedCornerShape(20.dp),
                elevation = CardDefaults.cardElevation(2.dp),
                colors = CardDefaults.cardColors(containerColor = cardColor),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Text(Strings.get("booking_reason"), fontSize = 11.sp,
                        color = textSec, fontWeight = FontWeight.Bold)
                    Spacer(Modifier.height(8.dp))
                    OutlinedTextField(
                        value = beschreibung, onValueChange = { beschreibung = it },
                        placeholder = { Text(Strings.get("booking_reason_hint")) },
                        minLines = 3, modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = OutlinedTextFieldDefaults.colors(focusedBorderColor = IDASBlue)
                    )
                }
            }

            Spacer(Modifier.height(16.dp))

            if (error.isNotEmpty()) {
                Surface(color = Color(0xFFFFEBEE), shape = RoundedCornerShape(12.dp),
                    modifier = Modifier.fillMaxWidth()) {
                    Row(modifier = Modifier.padding(12.dp),
                        verticalAlignment = Alignment.CenterVertically) {
                        Text("⚠️", fontSize = 16.sp); Spacer(Modifier.width(8.dp))
                        Text(error, color = IDASRed, fontSize = 13.sp)
                    }
                }
                Spacer(Modifier.height(12.dp))
            }

            Button(
                onClick = {
                    if (datum.isBlank()) {
                        error = Strings.get("booking_error_date"); return@Button
                    }
                    val fullDatum = "$datum $uhrzeit:00"
                    loading = true
                    scope.launch {
                        try {
                            val res = ApiService.buchTermin(token, arztId, fullDatum, beschreibung, symptomIds)
                            if (res.getBoolean("success")) {
                                onBooked(BookingConfirmData(
                                    terminId    = res.optInt("termin_id", 0),
                                    arztName    = arztName, fachbereich = fachbereich,
                                    datum       = "$datum um $uhrzeit Uhr",
                                    patientName = patientName, beschreibung = beschreibung
                                ))
                            } else error = res.optString("message", Strings.get("booking_error_fail"))
                        } catch (e: Exception) { error = Strings.get("error_server") }
                        finally { loading = false }
                    }
                },
                modifier = Modifier.fillMaxWidth().height(52.dp),
                colors = ButtonDefaults.buttonColors(containerColor = IDASGreen),
                shape = RoundedCornerShape(14.dp), enabled = !loading
            ) {
                if (loading) CircularProgressIndicator(color = Color.White,
                    strokeWidth = 2.dp, modifier = Modifier.size(22.dp))
                else Text(Strings.get("booking_confirm"), fontSize = 15.sp,
                    fontWeight = FontWeight.Bold)
            }
            Spacer(Modifier.height(20.dp))
        }
    }
}
