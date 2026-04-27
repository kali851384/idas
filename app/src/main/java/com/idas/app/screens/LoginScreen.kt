package com.idas.app.screens

import android.content.Context
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.network.ApiService
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings
import kotlinx.coroutines.launch

@Composable
fun LoginScreen(
    onLoginSuccess: (token: String, patientId: Int, name: String) -> Unit,
    onGoToRegister: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val scope     = rememberCoroutineScope()
    val context   = LocalContext.current
    var email     by remember { mutableStateOf("") }
    var pw        by remember { mutableStateOf("") }
    var pwVisible by remember { mutableStateOf(false) }
    var error     by remember { mutableStateOf("") }
    var loading      by remember { mutableStateOf(false) }
    var stayLoggedIn by remember { mutableStateOf(false) }


    fun doLogin() {
        if (email.isBlank() || pw.isBlank()) {
            error = Strings.get("login_error_empty"); return
        }
        loading = true
        scope.launch {
            try {
                val res = ApiService.login(email.trim(), pw)
                if (res.getBoolean("success")) {
                    val t   = res.getString("token")
                    val pid = res.getInt("patient_id")
                    val n   = res.getString("vorname")
                    if (stayLoggedIn) {
                        com.idas.app.utils.PrefsHelper.saveLogin(context, t, pid, n)
                    }
                    onLoginSuccess(t, pid, n)
                } else error = res.optString("message", Strings.get("login_error_fail"))
            } catch (e: Exception) {
                error = Strings.get("error_server")
            } finally { loading = false }
        }
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(listOf(IDASBlueDark, IDASBlue, Color(0xFF4A9FE8)))
            )
    ) {
        // Scrollable content
        Column(
            modifier = Modifier
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .imePadding()
                .navigationBarsPadding(),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Spacer(Modifier.height(64.dp))

            // Logo
            Box(
                modifier = Modifier
                    .size(90.dp)
                    .clip(RoundedCornerShape(26.dp))
                    .background(Color.White.copy(alpha = 0.2f)),
                contentAlignment = Alignment.Center
            ) { Text("🏥", fontSize = 48.sp) }

            Spacer(Modifier.height(16.dp))

            Text("IDAS", fontSize = 36.sp, fontWeight = FontWeight.Bold,
                color = Color.White, letterSpacing = 4.sp)
            Text("Gesundheitsportal", fontSize = 14.sp,
                color = Color.White.copy(0.8f), letterSpacing = 1.sp)

            Spacer(Modifier.height(40.dp))

            // Card
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 24.dp),
                shape = RoundedCornerShape(28.dp),
                colors = CardDefaults.cardColors(containerColor = cardColor),
                elevation = CardDefaults.cardElevation(20.dp)
            ) {
                Column(modifier = Modifier.padding(28.dp)) {
                    Text(Strings.get("login_title"), fontSize = 22.sp,
                        fontWeight = FontWeight.Bold, color = textPri)
                    Text(Strings.get("login_subtitle"), fontSize = 14.sp,
                        color = textSec,
                        modifier = Modifier.padding(top = 4.dp, bottom = 24.dp))

                    // Error message
                    if (error.isNotEmpty()) {
                        Surface(color = Color(0xFFFFEBEE), shape = RoundedCornerShape(12.dp),
                            modifier = Modifier.fillMaxWidth()) {
                            Row(modifier = Modifier.padding(12.dp),
                                verticalAlignment = Alignment.CenterVertically) {
                                Text("⚠️", fontSize = 16.sp)
                                Spacer(Modifier.width(8.dp))
                                Text(error, color = IDASRed, fontSize = 13.sp)
                            }
                        }
                        Spacer(Modifier.height(16.dp))
                    }

                    // Email
                    OutlinedTextField(
                        value = email, onValueChange = { email = it; error = "" },
                        label = { Text(Strings.get("login_email")) },
                        leadingIcon = { Icon(Icons.Default.Email, null, tint = IDASBlue) },
                        singleLine = true,
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(14.dp),
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = IDASBlue, focusedLabelColor = IDASBlue)
                    )

                    Spacer(Modifier.height(12.dp))

                    // Password
                    OutlinedTextField(
                        value = pw, onValueChange = { pw = it; error = "" },
                        label = { Text(Strings.get("login_password")) },
                        leadingIcon = { Icon(Icons.Default.Lock, null, tint = IDASBlue) },
                        trailingIcon = {
                            IconButton(onClick = { pwVisible = !pwVisible }) {
                                Icon(
                                    if (pwVisible) Icons.Default.VisibilityOff
                                    else Icons.Default.Visibility,
                                    null, tint = IDASTextSecondary
                                )
                            }
                        },
                        singleLine = true,
                        visualTransformation = if (pwVisible) VisualTransformation.None
                        else PasswordVisualTransformation(),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(14.dp),
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = IDASBlue, focusedLabelColor = IDASBlue)
                    )

                    Spacer(Modifier.height(12.dp))

                    // Stay logged in checkbox
                    Row(
                        modifier = Modifier.fillMaxWidth().padding(horizontal = 4.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Checkbox(
                            checked = stayLoggedIn,
                            onCheckedChange = { stayLoggedIn = it },
                            colors = CheckboxDefaults.colors(checkedColor = IDASBlue)
                        )
                        Text(
                            if (Strings.language == "de") "Angemeldet bleiben" else "Stay logged in",
                            fontSize = 14.sp, color = textPri,
                            modifier = Modifier.clickable { stayLoggedIn = !stayLoggedIn }
                        )
                    }

                    Spacer(Modifier.height(12.dp))

                    // Buttons row
                    Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                        Button(
                            onClick = { doLogin() },
                            modifier = Modifier.weight(1f).height(52.dp),
                            colors = ButtonDefaults.buttonColors(containerColor = IDASBlue),
                            shape = RoundedCornerShape(14.dp),
                            enabled = !loading
                        ) {
                            if (loading) CircularProgressIndicator(color = Color.White,
                                strokeWidth = 2.dp, modifier = Modifier.size(22.dp))
                            else Text(Strings.get("login_button"), fontSize = 16.sp,
                                fontWeight = FontWeight.Bold)
                        }

                    }

                    Spacer(Modifier.height(16.dp))

                    // Register link
                    Row(modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.Center,
                        verticalAlignment = Alignment.CenterVertically) {
                        Text(Strings.get("login_no_account"),
                            color = textSec, fontSize = 14.sp)
                        TextButton(onClick = onGoToRegister,
                            contentPadding = PaddingValues(0.dp)) {
                            Text(Strings.get("login_register"), color = IDASBlue,
                                fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        }
                    }
                }
            }

            Spacer(Modifier.height(40.dp))
        }
    }
}