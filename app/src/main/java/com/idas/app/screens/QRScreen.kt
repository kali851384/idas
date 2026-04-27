package com.idas.app.screens

import android.graphics.Bitmap
import android.graphics.Color
import com.google.zxing.BarcodeFormat
import com.google.zxing.EncodeHintType
import com.google.zxing.qrcode.QRCodeWriter
import com.google.zxing.qrcode.decoder.ErrorCorrectionLevel
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Share
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.asImageBitmap
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.models.BookingConfirmData
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings

fun generateQRBitmap(text: String, size: Int = 512): Bitmap {
    val hints = mapOf(
        EncodeHintType.ERROR_CORRECTION to ErrorCorrectionLevel.H,
        EncodeHintType.MARGIN to 2,
        EncodeHintType.CHARACTER_SET to "UTF-8"
    )
    val writer  = QRCodeWriter()
    val matrix  = writer.encode(text, BarcodeFormat.QR_CODE, size, size, hints)
    val bitmap  = Bitmap.createBitmap(size, size, Bitmap.Config.ARGB_8888)
    for (x in 0 until size) {
        for (y in 0 until size) {
            bitmap.setPixel(x, y, if (matrix[x, y]) Color.BLACK else Color.WHITE)
        }
    }
    return bitmap
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun QRScreen(
    termin: BookingConfirmData?,
    onBack: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val context = LocalContext.current

    val qrText = termin?.let {
        "IDAS Termin | Ticket:#${it.terminId} | Arzt:${it.arztName} | Fach:${it.fachbereich} | Datum:${it.datum} | Patient:${it.patientName}"
    } ?: "IDAS Gesundheitsportal"

    val qrBitmap = remember(qrText) {
        try { generateQRBitmap(qrText) } catch (e: Exception) { null }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("QR-Code") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.Default.ArrowBack, Strings.get("back"))
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = IDASBlue,
                    titleContentColor = androidx.compose.ui.graphics.Color.White,
                    navigationIconContentColor = androidx.compose.ui.graphics.Color.White
                )
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(bg)
                .padding(padding)
                .padding(24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Spacer(Modifier.height(16.dp))

            Text("Ihr Termin-QR-Code", fontSize = 22.sp,
                fontWeight = FontWeight.Bold, color = textPri)
            Text("Zeigen Sie diesen Code beim Arzt vor",
                fontSize = 14.sp, color = textSec,
                modifier = Modifier.padding(top = 4.dp, bottom = 24.dp),
                textAlign = TextAlign.Center)

            // QR Code card
            Card(
                shape = RoundedCornerShape(20.dp),
                elevation = CardDefaults.cardElevation(4.dp),
                colors = CardDefaults.cardColors(
                    containerColor = androidx.compose.ui.graphics.Color.White),
                modifier = Modifier.size(280.dp)
            ) {
                Box(modifier = Modifier.fillMaxSize().padding(16.dp),
                    contentAlignment = Alignment.Center) {
                    if (qrBitmap != null) {
                        Image(
                            bitmap = qrBitmap.asImageBitmap(),
                            contentDescription = "QR Code",
                            modifier = Modifier.fillMaxSize()
                        )
                    } else {
                        Text("QR konnte nicht generiert werden.",
                            color = IDASRed, textAlign = TextAlign.Center)
                    }
                }
            }

            Spacer(Modifier.height(24.dp))

            // Ticket info
            if (termin != null) {
                Card(
                    shape = RoundedCornerShape(16.dp),
                    elevation = CardDefaults.cardElevation(2.dp),
                    colors = CardDefaults.cardColors(
                        containerColor = androidx.compose.ui.graphics.Color.White),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Row(modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween) {
                            Text("Ticket", fontSize = 12.sp, color = textSec)
                            Text("#${termin.terminId}", fontSize = 14.sp,
                                fontWeight = FontWeight.Bold, color = IDASBlue)
                        }
                        Divider(modifier = Modifier.padding(vertical = 8.dp), color = border)
                        ConfirmRow("🏥 Arzt",        termin.arztName)
                        ConfirmRow("🔬 Fachbereich", termin.fachbereich)
                        ConfirmRow("📅 Datum",        termin.datum)
                        ConfirmRow("👤 Patient",      termin.patientName)
                    }
                }
            }

            Spacer(Modifier.height(20.dp))

            Button(
                onClick = { termin?.let { generateAndSharePdf(context, it) } },
                modifier = Modifier.fillMaxWidth().height(50.dp),
                colors = ButtonDefaults.buttonColors(containerColor = IDASBlue),
                shape = RoundedCornerShape(14.dp)
            ) {
                Icon(Icons.Default.Share, null, modifier = Modifier.size(18.dp))
                Spacer(Modifier.width(8.dp))
                Text("Termin teilen", fontSize = 14.sp, fontWeight = FontWeight.Bold)
            }
        }
    }
}
