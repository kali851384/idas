package com.idas.app.screens

import android.content.Intent
import android.net.Uri
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.models.Arzt
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DoctorDetailScreen(
    arzt: Arzt,
    fachbereich: String,
    onBack: () -> Unit,
    onBook: (Int, String, String) -> Unit
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
                title = { Text(arzt.name) },
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
        ) {
            // Hero
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(Brush.horizontalGradient(listOf(IDASBlueDark, IDASBlue)))
                    .padding(24.dp),
                contentAlignment = Alignment.Center
            ) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Box(
                        modifier = Modifier
                            .size(90.dp)
                            .clip(CircleShape)
                            .background(Color.White.copy(0.2f)),
                        contentAlignment = Alignment.Center
                    ) {
                        Text(
                            arzt.name.split(" ").mapNotNull { it.firstOrNull()?.uppercaseChar() }.take(2).joinToString(""),
                            fontSize = 32.sp, fontWeight = FontWeight.Bold, color = Color.White
                        )
                    }
                    Spacer(Modifier.height(12.dp))
                    Text(arzt.name, fontSize = 20.sp, fontWeight = FontWeight.Bold, color = Color.White)
                    Surface(
                        color = Color.White.copy(0.2f),
                        shape = RoundedCornerShape(8.dp),
                        modifier = Modifier.padding(top = 8.dp)
                    ) {
                        Text(fachbereich,
                            modifier = Modifier.padding(horizontal = 12.dp, vertical = 5.dp),
                            fontSize = 13.sp, color = Color.White, fontWeight = FontWeight.Bold)
                    }
                }
            }

            Column(modifier = Modifier.padding(16.dp)) {

                // Info card
                Card(
                    shape = RoundedCornerShape(20.dp),
                    elevation = CardDefaults.cardElevation(2.dp),
                    colors = CardDefaults.cardColors(containerColor = cardColor),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(modifier = Modifier.padding(20.dp)) {
                        Text("Kontaktinformationen", fontWeight = FontWeight.Bold,
                            fontSize = 15.sp, color = textPri)
                        Divider(modifier = Modifier.padding(vertical = 12.dp), color = border)

                        if (arzt.addresse.isNotBlank()) {
                            InfoRow(icon = Icons.Default.LocationOn,
                                label = "Adresse", value = arzt.addresse)
                        }
                        if (arzt.telefon.isNotBlank()) {
                            InfoRow(icon = Icons.Default.Phone,
                                label = "Telefon", value = arzt.telefon)
                        }
                        if (arzt.email.isNotBlank()) {
                            InfoRow(icon = Icons.Default.Email,
                                label = "E-Mail", value = arzt.email)
                        }
                    }
                }

                Spacer(Modifier.height(16.dp))

                // Action buttons
                if (arzt.telefon.isNotBlank()) {
                    OutlinedButton(
                        onClick = {
                            val intent = Intent(Intent.ACTION_DIAL,
                                Uri.parse("tel:${arzt.telefon}"))
                            context.startActivity(intent)
                        },
                        modifier = Modifier.fillMaxWidth().height(50.dp),
                        shape = RoundedCornerShape(14.dp),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = IDASBlue)
                    ) {
                        Icon(Icons.Default.Phone, null, modifier = Modifier.size(18.dp))
                        Spacer(Modifier.width(8.dp))
                        Text("Anrufen", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                    }
                    Spacer(Modifier.height(10.dp))
                }

                if (arzt.addresse.isNotBlank()) {
                    OutlinedButton(
                        onClick = {
                            val uri = Uri.parse("geo:0,0?q=${Uri.encode(arzt.addresse)}")
                            val intent = Intent(Intent.ACTION_VIEW, uri)
                            context.startActivity(intent)
                        },
                        modifier = Modifier.fillMaxWidth().height(50.dp),
                        shape = RoundedCornerShape(14.dp),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = IDASGreen)
                    ) {
                        Icon(Icons.Default.LocationOn, null, modifier = Modifier.size(18.dp))
                        Spacer(Modifier.width(8.dp))
                        Text("In Karte öffnen", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                    }
                    Spacer(Modifier.height(10.dp))
                }

                Button(
                    onClick = { onBook(arzt.arztId, arzt.name, fachbereich) },
                    modifier = Modifier.fillMaxWidth().height(52.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = IDASBlue),
                    shape = RoundedCornerShape(14.dp)
                ) {
                    Icon(Icons.Default.CalendarMonth, null, modifier = Modifier.size(18.dp))
                    Spacer(Modifier.width(8.dp))
                    Text(Strings.get("matching_book"), fontSize = 15.sp, fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}

@Composable
fun InfoRow(icon: ImageVector, label: String, value: String) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    Row(modifier = Modifier.fillMaxWidth().padding(vertical = 8.dp),
        verticalAlignment = Alignment.Top) {
        Icon(icon, null, tint = IDASBlue, modifier = Modifier.size(18.dp).padding(top = 2.dp))
        Spacer(Modifier.width(12.dp))
        Column {
            Text(label, fontSize = 11.sp, color = textSec, fontWeight = FontWeight.Bold)
            Text(value, fontSize = 13.sp, color = textPri, modifier = Modifier.padding(top = 2.dp))
        }
    }
    Divider(color = border, thickness = 0.5.dp)
}
