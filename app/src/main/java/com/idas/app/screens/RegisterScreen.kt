package com.idas.app.screens

import androidx.compose.foundation.background
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
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
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
import androidx.compose.ui.draw.clip

fun passwordStrength(pw: String): Triple<Float, Color, String> {
    var score = 0
    if (pw.length >= 8)              score++
    if (pw.any { it.isUpperCase() }) score++
    if (pw.any { it.isDigit() })     score++
    if (pw.any { "!@#\$%^&*".contains(it) }) score++
    return when (score) {
        0, 1 -> Triple(0.25f, Color(0xFFE53E3E), "Schwach")
        2    -> Triple(0.5f,  Color(0xFFED8936), "Mittel")
        3    -> Triple(0.75f, Color(0xFFECC94B), "Gut")
        else -> Triple(1f,    Color(0xFF48BB78), "Stark")
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun RegisterScreen(
    onRegisterSuccess: () -> Unit,
    onBack: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val scope    = rememberCoroutineScope()
    var vorname  by remember { mutableStateOf("") }
    var nachname by remember { mutableStateOf("") }
    var email    by remember { mutableStateOf("") }
    var telefon  by remember { mutableStateOf("") }
    var pw       by remember { mutableStateOf("") }
    var pw2      by remember { mutableStateOf("") }
    var pwVisible  by remember { mutableStateOf(false) }
    var pw2Visible by remember { mutableStateOf(false) }
    var error    by remember { mutableStateOf("") }
    var loading  by remember { mutableStateOf(false) }

    // Validation
    val emailValid  = email.contains("@") && email.contains(".")
    val pwMatch     = pw == pw2 && pw2.isNotEmpty()
    val (pwProgress, pwColor, pwLabel) = passwordStrength(pw)
    val formValid   = vorname.isNotBlank() && nachname.isNotBlank() &&
                      emailValid && pw.length >= 6 && pwMatch

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(Strings.get("register_title")) },
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
            modifier = Modifier.fillMaxSize()
                .background(bg)
                .padding(padding)
                .verticalScroll(rememberScrollState())
                .imePadding()
        ) {
            // Header
            Box(modifier = Modifier.fillMaxWidth()
                .background(Brush.verticalGradient(listOf(IDASBlue, Color(0xFF3D8FE0))))
                .padding(24.dp)) {
                Column {
                    Text("🏥", fontSize = 36.sp)
                    Spacer(Modifier.height(8.dp))
                    Text(Strings.get("register_subtitle"),
                        fontSize = 15.sp, color = Color.White.copy(0.85f))
                }
            }

            Column(modifier = Modifier.padding(20.dp)) {
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

                Card(shape = RoundedCornerShape(20.dp),
                    elevation = CardDefaults.cardElevation(2.dp),
                    colors = CardDefaults.cardColors(containerColor = cardColor),
                    modifier = Modifier.fillMaxWidth()) {
                    Column(modifier = Modifier.padding(20.dp)) {
                        Text(if (Strings.language == "de") "Persönliche Daten" else "Personal Info",
                            fontWeight = FontWeight.Bold, fontSize = 15.sp,
                            color = textPri, modifier = Modifier.padding(bottom = 16.dp))

                        // Name row
                        Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                            RegField(Strings.get("profil_vorname"), vorname, Icons.Default.Person,
                                modifier = Modifier.weight(1f)) { vorname = it }
                            RegField(Strings.get("profil_nachname"), nachname, Icons.Default.Person,
                                modifier = Modifier.weight(1f)) { nachname = it }
                        }
                        Spacer(Modifier.height(12.dp))

                        // Email with validation indicator
                        OutlinedTextField(
                            value = email, onValueChange = { email = it; error = "" },
                            label = { Text(Strings.get("login_email")) },
                            leadingIcon = { Icon(Icons.Default.Email, null, tint = IDASBlue) },
                            trailingIcon = {
                                if (email.isNotEmpty())
                                    Icon(if (emailValid) Icons.Default.CheckCircle else Icons.Default.Cancel,
                                        null, tint = if (emailValid) IDASGreen else IDASRed,
                                        modifier = Modifier.size(20.dp))
                            },
                            singleLine = true,
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedBorderColor = IDASBlue, focusedLabelColor = IDASBlue)
                        )
                        Spacer(Modifier.height(12.dp))

                        OutlinedTextField(
                            value = telefon, onValueChange = { telefon = it },
                            label = { Text(Strings.get("profil_telefon")) },
                            leadingIcon = { Icon(Icons.Default.Phone, null, tint = IDASBlue) },
                            singleLine = true,
                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Phone),
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedBorderColor = IDASBlue, focusedLabelColor = IDASBlue)
                        )
                    }
                }

                Spacer(Modifier.height(16.dp))

                Card(shape = RoundedCornerShape(20.dp),
                    elevation = CardDefaults.cardElevation(2.dp),
                    colors = CardDefaults.cardColors(containerColor = cardColor),
                    modifier = Modifier.fillMaxWidth()) {
                    Column(modifier = Modifier.padding(20.dp)) {
                        Text(if (Strings.language == "de") "Passwort" else "Password",
                            fontWeight = FontWeight.Bold, fontSize = 15.sp,
                            color = textPri, modifier = Modifier.padding(bottom = 16.dp))

                        OutlinedTextField(
                            value = pw, onValueChange = { pw = it; error = "" },
                            label = { Text(Strings.get("login_password")) },
                            leadingIcon = { Icon(Icons.Default.Lock, null, tint = IDASBlue) },
                            trailingIcon = {
                                IconButton(onClick = { pwVisible = !pwVisible }) {
                                    Icon(if (pwVisible) Icons.Default.VisibilityOff
                                         else Icons.Default.Visibility, null,
                                        tint = textSec)
                                }
                            },
                            visualTransformation = if (pwVisible) VisualTransformation.None
                                else PasswordVisualTransformation(),
                            singleLine = true,
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedBorderColor = IDASBlue, focusedLabelColor = IDASBlue)
                        )

                        // Password strength bar
                        if (pw.isNotEmpty()) {
                            Spacer(Modifier.height(8.dp))
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                LinearProgressIndicator(
                                    progress = { pwProgress },
                                    modifier = Modifier.weight(1f).height(6.dp)
                                        .clip(RoundedCornerShape(3.dp)),
                                    color = pwColor,
                                    trackColor = IDASBackground
                                )
                                Spacer(Modifier.width(10.dp))
                                Text(pwLabel, fontSize = 12.sp, color = pwColor,
                                    fontWeight = FontWeight.Bold)
                            }
                            Spacer(Modifier.height(6.dp))
                            // Requirements
                            Column(verticalArrangement = Arrangement.spacedBy(2.dp)) {
                                PwRequirement("Mindestens 8 Zeichen", pw.length >= 8)
                                PwRequirement("Großbuchstabe (A-Z)", pw.any { it.isUpperCase() })
                                PwRequirement("Zahl (0-9)", pw.any { it.isDigit() })
                            }
                        }

                        Spacer(Modifier.height(12.dp))

                        OutlinedTextField(
                            value = pw2, onValueChange = { pw2 = it; error = "" },
                            label = { Text(Strings.get("register_pw2")) },
                            leadingIcon = { Icon(Icons.Default.Lock, null, tint = IDASBlue) },
                            trailingIcon = {
                                IconButton(onClick = { pw2Visible = !pw2Visible }) {
                                    Icon(if (pw2Visible) Icons.Default.VisibilityOff
                                         else Icons.Default.Visibility, null,
                                        tint = textSec)
                                }
                                if (pw2.isNotEmpty())
                                    Icon(if (pwMatch) Icons.Default.CheckCircle
                                         else Icons.Default.Cancel, null,
                                        tint = if (pwMatch) IDASGreen else IDASRed)
                            },
                            visualTransformation = if (pw2Visible) VisualTransformation.None
                                else PasswordVisualTransformation(),
                            singleLine = true,
                            isError = pw2.isNotEmpty() && !pwMatch,
                            supportingText = {
                                if (pw2.isNotEmpty() && !pwMatch)
                                    Text(if (Strings.language == "de") "Passwörter stimmen nicht überein"
                                         else "Passwords don't match",
                                        color = IDASRed, fontSize = 12.sp)
                            },
                            modifier = Modifier.fillMaxWidth(),
                            shape = RoundedCornerShape(12.dp),
                            colors = OutlinedTextFieldDefaults.colors(
                                focusedBorderColor = IDASBlue, focusedLabelColor = IDASBlue)
                        )
                    }
                }

                Spacer(Modifier.height(20.dp))

                Button(
                    onClick = {
                        if (!emailValid) { error = Strings.get("register_error_email"); return@Button }
                        if (pw.length < 6)  { error = Strings.get("register_error_pw"); return@Button }
                        if (!pwMatch)        { error = Strings.get("register_error_pw2"); return@Button }
                        loading = true
                        scope.launch {
                            try {
                                val res = ApiService.register(vorname, nachname, email, pw, "", "")
                                if (res.getBoolean("success")) onRegisterSuccess()
                                else error = res.optString("message", "Registrierung fehlgeschlagen.")
                            } catch (e: Exception) { error = Strings.get("error_server") }
                            finally { loading = false }
                        }
                    },
                    modifier = Modifier.fillMaxWidth().height(52.dp),
                    colors = ButtonDefaults.buttonColors(
                        containerColor = if (formValid) IDASGreen else IDASBorder),
                    shape = RoundedCornerShape(14.dp),
                    enabled = !loading
                ) {
                    if (loading) CircularProgressIndicator(color = Color.White,
                        strokeWidth = 2.dp, modifier = Modifier.size(22.dp))
                    else {
                        Icon(Icons.Default.PersonAdd, null, modifier = Modifier.size(18.dp))
                        Spacer(Modifier.width(8.dp))
                        Text(Strings.get("register_button"),
                            fontSize = 15.sp, fontWeight = FontWeight.Bold)
                    }
                }

                Spacer(Modifier.height(12.dp))
                Row(modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.Center,
                    verticalAlignment = Alignment.CenterVertically) {
                    Text(Strings.get("register_have_account"),
                        color = textSec, fontSize = 14.sp)
                    TextButton(onClick = onBack, contentPadding = PaddingValues(0.dp)) {
                        Text(Strings.get("login_button"), color = IDASBlue,
                            fontSize = 14.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }
            Spacer(Modifier.height(20.dp))
        }
    }
}

@Composable
fun RegField(label: String, value: String, icon: androidx.compose.ui.graphics.vector.ImageVector,
             modifier: Modifier = Modifier, onChange: (String) -> Unit) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    OutlinedTextField(value = value, onValueChange = onChange,
        label = { Text(label, fontSize = 12.sp) },
        leadingIcon = { Icon(icon, null, tint = IDASBlue, modifier = Modifier.size(18.dp)) },
        singleLine = true, modifier = modifier,
        shape = RoundedCornerShape(12.dp),
        colors = OutlinedTextFieldDefaults.colors(
            focusedBorderColor = IDASBlue, focusedLabelColor = IDASBlue))
}

@Composable
fun PwRequirement(text: String, met: Boolean) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    Row(verticalAlignment = Alignment.CenterVertically) {
        Text(if (met) "✓" else "○", fontSize = 12.sp,
            color = if (met) IDASGreen else IDASTextSecondary)
        Spacer(Modifier.width(6.dp))
        Text(text, fontSize = 12.sp,
            color = if (met) IDASGreenDark else IDASTextSecondary)
    }
}
