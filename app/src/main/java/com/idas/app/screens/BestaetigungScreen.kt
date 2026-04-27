package com.idas.app.screens

import android.content.Context
import android.content.Intent
import android.graphics.Paint as AndroidPaint
import android.graphics.pdf.PdfDocument
import android.widget.Toast
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Share
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.core.content.FileProvider
import com.idas.app.models.BookingConfirmData
import com.idas.app.ui.theme.*
import java.io.File
import java.io.FileOutputStream

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun BestaetigungScreen(
    termin: BookingConfirmData?,
    onHome: () -> Unit,
    onTermine: () -> Unit,
    onQRCode: () -> Unit = {}
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val context = LocalContext.current

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Buchungsbestätigung") },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = IDASGreen,
                    titleContentColor = Color.White
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
                .padding(20.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Spacer(Modifier.height(16.dp))

            // Success circle
            Box(
                modifier = Modifier
                    .size(100.dp)
                    .clip(CircleShape)
                    .background(Brush.radialGradient(
                        listOf(Color(0xFF00E096), IDASGreen)
                    )),
                contentAlignment = Alignment.Center
            ) {
                Icon(Icons.Default.CheckCircle, null,
                    tint = Color.White, modifier = Modifier.size(60.dp))
            }

            Spacer(Modifier.height(16.dp))
            Text("Termin gebucht!", fontSize = 24.sp, fontWeight = FontWeight.Bold,
                color = textPri)
            Text("Ihr Termin wurde erfolgreich reserviert.",
                fontSize = 14.sp, color = textSec,
                modifier = Modifier.padding(top = 4.dp, bottom = 24.dp))

            if (termin != null) {
                // Details card
                Card(
                    shape = RoundedCornerShape(20.dp),
                    elevation = CardDefaults.cardElevation(4.dp),
                    colors = CardDefaults.cardColors(containerColor = cardColor),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(modifier = Modifier.padding(20.dp)) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Text("📋", fontSize = 20.sp)
                            Spacer(Modifier.width(8.dp))
                            Text("Termindetails", fontWeight = FontWeight.Bold,
                                fontSize = 16.sp, color = textPri)
                        }
                        Divider(modifier = Modifier.padding(vertical = 12.dp), color = border)

                        ConfirmRow("🏥 Arzt",        termin.arztName)
                        ConfirmRow("🔬 Fachbereich", termin.fachbereich)
                        ConfirmRow("📅 Datum",        termin.datum)
                        ConfirmRow("👤 Patient",      termin.patientName)
                        if (termin.beschreibung.isNotBlank())
                            ConfirmRow("📝 Grund", termin.beschreibung)

                        Spacer(Modifier.height(8.dp))
                        Surface(
                            color = blueGray,
                            shape = RoundedCornerShape(10.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Row(modifier = Modifier.padding(12.dp),
                                verticalAlignment = Alignment.CenterVertically) {
                                Text("🎫", fontSize = 16.sp)
                                Spacer(Modifier.width(8.dp))
                                Text("Ticket #${termin.terminId}",
                                    fontWeight = FontWeight.Bold, color = IDASBlue, fontSize = 14.sp)
                            }
                        }
                    }
                }

                Spacer(Modifier.height(16.dp))

                // PDF Button
                Button(
                    onClick = { generateAndSharePdf(context, termin) },
                    modifier = Modifier.fillMaxWidth().height(50.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = IDASBlue),
                    shape = RoundedCornerShape(14.dp)
                ) {
                    Icon(Icons.Default.Share, null, modifier = Modifier.size(18.dp))
                    Spacer(Modifier.width(8.dp))
                    Text("Als PDF speichern / teilen",
                        fontSize = 14.sp, fontWeight = FontWeight.Bold)
                }

                Spacer(Modifier.height(10.dp))
            }

            Button(
                onClick = onQRCode,
                modifier = Modifier.fillMaxWidth().height(50.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF6A1B9A)),
                shape = RoundedCornerShape(14.dp)
            ) {
                Text("🔲  QR-Code anzeigen", fontSize = 14.sp, fontWeight = FontWeight.Bold)
            }

            Spacer(Modifier.height(10.dp))

            OutlinedButton(
                onClick = onTermine,
                modifier = Modifier.fillMaxWidth().height(50.dp),
                shape = RoundedCornerShape(14.dp),
                colors = ButtonDefaults.outlinedButtonColors(contentColor = IDASBlue)
            ) {
                Text("Meine Termine ansehen", fontSize = 14.sp)
                Spacer(Modifier.width(8.dp))
                Text("Meine Termine ansehen", fontSize = 14.sp)
            }

            Spacer(Modifier.height(10.dp))

            TextButton(onClick = onHome) {
                Text("Zurück zum Dashboard", color = textSec)
            }
        }
    }
}

@Composable
fun ConfirmRow(label: String, value: String) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    Row(modifier = Modifier.fillMaxWidth().padding(vertical = 6.dp),
        verticalAlignment = Alignment.Top) {
        Text(label, fontSize = 13.sp, color = textSec,
            modifier = Modifier.width(140.dp))
        Text(value.ifBlank { "—" }, fontSize = 13.sp,
            fontWeight = FontWeight.Medium, color = textPri,
            modifier = Modifier.weight(1f))
    }
}

fun generateAndSharePdf(context: Context, termin: BookingConfirmData) {
    try {
        val doc  = PdfDocument()
        val info = PdfDocument.PageInfo.Builder(595, 842, 1).create()
        val page = doc.startPage(info)
        val canvas = page.canvas

        val headerPaint = AndroidPaint().apply { color = android.graphics.Color.rgb(30, 111, 217) }
        canvas.drawRect(0f, 0f, 595f, 60f, headerPaint)

        val whPaint = AndroidPaint().apply {
            textSize = 20f; color = android.graphics.Color.WHITE; isFakeBoldText = true
        }
        canvas.drawText("IDAS — Buchungsbestätigung", 24f, 38f, whPaint)

        val titlePaint = AndroidPaint().apply {
            textSize = 18f; color = android.graphics.Color.rgb(13, 27, 42); isFakeBoldText = true
        }
        val labelPaint = AndroidPaint().apply {
            textSize = 11f; color = android.graphics.Color.rgb(107, 122, 141); isFakeBoldText = true
        }
        val valuePaint = AndroidPaint().apply {
            textSize = 14f; color = android.graphics.Color.rgb(13, 27, 42)
        }
        val linePaint = AndroidPaint().apply {
            color = android.graphics.Color.rgb(226, 232, 240); strokeWidth = 1f
        }
        val smallPaint = AndroidPaint().apply {
            textSize = 10f; color = android.graphics.Color.rgb(150, 150, 150)
        }

        canvas.drawText("Terminbestätigung", 40f, 100f, titlePaint)
        canvas.drawLine(40f, 112f, 555f, 112f, linePaint)

        var y = 140f
        fun drawField(label: String, value: String) {
            canvas.drawText(label.uppercase(), 40f, y, labelPaint)
            canvas.drawText(value.ifBlank { "—" }, 40f, y + 20f, valuePaint)
            y += 50f
        }

        drawField("Arzt",        termin.arztName)
        drawField("Fachbereich", termin.fachbereich)
        drawField("Datum",       termin.datum)
        drawField("Patient",     termin.patientName)
        if (termin.beschreibung.isNotBlank()) drawField("Grund", termin.beschreibung)
        drawField("Ticket-Nr.",  "#${termin.terminId}")

        canvas.drawLine(40f, 800f, 555f, 800f, linePaint)
        canvas.drawText("IDAS Gesundheitsportal — Vertraulich", 40f, 820f, smallPaint)
        canvas.drawText(
            java.text.SimpleDateFormat("dd.MM.yyyy HH:mm", java.util.Locale.GERMANY)
                .format(java.util.Date()), 450f, 820f, smallPaint)

        doc.finishPage(page)
        val file = File(context.cacheDir, "termin_${termin.terminId}.pdf")
        doc.writeTo(FileOutputStream(file))
        doc.close()

        val uri = FileProvider.getUriForFile(context, "${context.packageName}.fileprovider", file)
        val intent = Intent(Intent.ACTION_SEND).apply {
            type = "application/pdf"
            putExtra(Intent.EXTRA_STREAM, uri)
            putExtra(Intent.EXTRA_SUBJECT, "IDAS Terminbestätigung")
            addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
        }
        context.startActivity(Intent.createChooser(intent, "PDF teilen"))
    } catch (e: Exception) {
        Toast.makeText(context, "Fehler: ${e.message}", Toast.LENGTH_LONG).show()
    }
}

