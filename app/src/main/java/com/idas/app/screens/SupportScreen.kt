package com.idas.app.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Send
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.network.ApiService
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings
import kotlinx.coroutines.launch

data class SupportTicket(
    val ticketId: Int,
    val betreff:  String,
    val problem:  String,
    val status:   String,
    val datum:    String,
    val antwort:  String
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SupportScreen(token: String, onBack: () -> Unit) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val scope   = rememberCoroutineScope()
    var betreff by remember { mutableStateOf("") }
    var problem by remember { mutableStateOf("") }
    var loading by remember { mutableStateOf(false) }
    var success by remember { mutableStateOf("") }
    var error   by remember { mutableStateOf("") }
    var tickets by remember { mutableStateOf<List<SupportTicket>>(emptyList()) }
    var tab     by remember { mutableStateOf(0) }

    fun loadTickets() {
        scope.launch {
            try {
                val res = ApiService.getSupportTickets(token)
                if (res.getBoolean("success")) {
                    val arr = res.getJSONArray("data")
                    tickets = (0 until arr.length()).map {
                        val obj = arr.getJSONObject(it)
                        SupportTicket(
                            ticketId = obj.getInt("ticket_id"),
                            betreff  = obj.optString("betreff", "—"),
                            problem  = obj.optString("problembeschreibung", ""),
                            status   = obj.optString("status", "offen"),
                            datum    = obj.optString("datum", ""),
                            antwort  = obj.optString("antwort", "")
                        )
                    }
                }
            } catch (e: Exception) { }
        }
    }

    LaunchedEffect(Unit) { loadTickets() }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(Strings.get("support_title")) },
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
            // Tabs
            TabRow(selectedTabIndex = tab, containerColor = cardColor) {
                Tab(selected = tab == 0, onClick = { tab = 0 }) {
                    Text(
                        if (Strings.language == "de") "Neues Ticket" else "New Ticket",
                        modifier = Modifier.padding(vertical = 14.dp),
                        fontWeight = if (tab == 0) FontWeight.Bold else FontWeight.Normal
                    )
                }
                Tab(selected = tab == 1, onClick = { tab = 1; loadTickets() }) {
                    Text(
                        Strings.get("support_my"),
                        modifier = Modifier.padding(vertical = 14.dp),
                        fontWeight = if (tab == 1) FontWeight.Bold else FontWeight.Normal
                    )
                }
            }

            when (tab) {
                0 -> Column(
                    modifier = Modifier
                        .fillMaxSize()
                        .verticalScroll(rememberScrollState())
                        .padding(16.dp)
                ) {
                    if (success.isNotEmpty()) {
                        Surface(
                            color = Color(0xFFE8F5E9),
                            shape = RoundedCornerShape(12.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Row(
                                modifier = Modifier.padding(12.dp),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Text("✅", fontSize = 16.sp)
                                Spacer(Modifier.width(8.dp))
                                Text(success, color = IDASGreenDark, fontSize = 13.sp)
                            }
                        }
                        Spacer(Modifier.height(12.dp))
                    }

                    if (error.isNotEmpty()) {
                        Surface(
                            color = Color(0xFFFFEBEE),
                            shape = RoundedCornerShape(12.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Row(
                                modifier = Modifier.padding(12.dp),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Text("⚠️", fontSize = 16.sp)
                                Spacer(Modifier.width(8.dp))
                                Text(error, color = IDASRed, fontSize = 13.sp)
                            }
                        }
                        Spacer(Modifier.height(12.dp))
                    }

                    Card(
                        shape = RoundedCornerShape(20.dp),
                        elevation = CardDefaults.cardElevation(2.dp),
                        colors = CardDefaults.cardColors(containerColor = cardColor),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Column(modifier = Modifier.padding(20.dp)) {
                            Text(
                                if (Strings.language == "de") "Support kontaktieren" else "Contact Support",
                                fontWeight = FontWeight.Bold, fontSize = 16.sp, color = textPri
                            )
                            Text(
                                if (Strings.language == "de") "Unser Team hilft Ihnen gerne weiter."
                                else "Our team is happy to help you.",
                                fontSize = 13.sp, color = textSec,
                                modifier = Modifier.padding(top = 4.dp, bottom = 16.dp)
                            )

                            OutlinedTextField(
                                value = betreff,
                                onValueChange = { betreff = it; error = "" },
                                label = { Text(Strings.get("support_betreff")) },
                                singleLine = true,
                                modifier = Modifier.fillMaxWidth(),
                                shape = RoundedCornerShape(12.dp),
                                colors = OutlinedTextFieldDefaults.colors(
                                    focusedBorderColor = IDASBlue,
                                    focusedLabelColor  = IDASBlue
                                )
                            )
                            Spacer(Modifier.height(12.dp))
                            OutlinedTextField(
                                value = problem,
                                onValueChange = { problem = it; error = "" },
                                label = { Text(Strings.get("support_problem")) },
                                minLines = 5,
                                modifier = Modifier.fillMaxWidth(),
                                shape = RoundedCornerShape(12.dp),
                                colors = OutlinedTextFieldDefaults.colors(
                                    focusedBorderColor = IDASBlue,
                                    focusedLabelColor  = IDASBlue
                                )
                            )

                            Spacer(Modifier.height(20.dp))

                            Button(
                                onClick = {
                                    if (betreff.isBlank() || problem.isBlank()) {
                                        error = if (Strings.language == "de")
                                            "Bitte alle Felder ausfüllen."
                                        else "Please fill in all fields."
                                        return@Button
                                    }
                                    loading = true
                                    error   = ""
                                    success = ""
                                    scope.launch {
                                        try {
                                            val res = ApiService.createSupportTicket(
                                                token, betreff, problem
                                            )
                                            if (res.getBoolean("success")) {
                                                success = Strings.get("support_success")
                                                betreff = ""
                                                problem = ""
                                                loadTickets()
                                            } else {
                                                error = res.optString("message", "Fehler.")
                                            }
                                        } catch (e: Exception) {
                                            error = Strings.get("error_server")
                                        } finally { loading = false }
                                    }
                                },
                                modifier = Modifier.fillMaxWidth().height(50.dp),
                                colors = ButtonDefaults.buttonColors(containerColor = IDASBlue),
                                shape = RoundedCornerShape(14.dp),
                                enabled = !loading
                            ) {
                                if (loading) {
                                    CircularProgressIndicator(
                                        color = Color.White, strokeWidth = 2.dp,
                                        modifier = Modifier.size(20.dp)
                                    )
                                } else {
                                    Icon(Icons.Default.Send, null,
                                        modifier = Modifier.size(18.dp))
                                    Spacer(Modifier.width(8.dp))
                                    Text(Strings.get("support_send"), fontWeight = FontWeight.Bold)
                                }
                            }
                        }
                    }
                }

                1 -> {
                    if (tickets.isEmpty()) {
                        Box(
                            Modifier.fillMaxSize(),
                            Alignment.Center
                        ) {
                            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                Text("🎫", fontSize = 48.sp)
                                Spacer(Modifier.height(12.dp))
                                Text(Strings.get("support_empty"),
                                    color = textSec, fontSize = 14.sp)
                            }
                        }
                    } else {
                        LazyColumn(
                            contentPadding = PaddingValues(16.dp),
                            verticalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            items(tickets) { ticket ->
                                TicketCard(ticket)
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun TicketCard(ticket: SupportTicket) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val statusColor = when (ticket.status) {
        "offen"          -> IDASRed
        "in_bearbeitung" -> Color(0xFFE67E22)
        else             -> IDASGreen
    }
    val statusLabel = when (ticket.status) {
        "offen"          -> Strings.get("support_open")
        "in_bearbeitung" -> Strings.get("support_progress")
        else             -> Strings.get("support_closed")
    }

    Card(
        shape = RoundedCornerShape(16.dp),
        elevation = CardDefaults.cardElevation(2.dp),
        colors = CardDefaults.cardColors(containerColor = cardColor),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(3.dp)
                    .background(statusColor)
            )
            Column(modifier = Modifier.padding(16.dp)) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Text(
                        ticket.betreff, fontWeight = FontWeight.Bold,
                        fontSize = 14.sp, color = textPri,
                        modifier = Modifier.weight(1f)
                    )
                    Surface(
                        color = statusColor.copy(0.12f),
                        shape = RoundedCornerShape(8.dp)
                    ) {
                        Text(
                            statusLabel,
                            modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp),
                            fontSize = 11.sp, color = statusColor, fontWeight = FontWeight.Bold
                        )
                    }
                }
                Spacer(Modifier.height(8.dp))
                Text(ticket.problem, fontSize = 13.sp,
                    color = textSec, maxLines = 2)

                if (ticket.antwort.isNotBlank()) {
                    Spacer(Modifier.height(10.dp))
                    Divider(color = border)
                    Spacer(Modifier.height(10.dp))
                    Row {
                        Text("💬 ", fontSize = 14.sp)
                        Text(ticket.antwort, fontSize = 13.sp, color = textPri)
                    }
                }
                if (ticket.datum.isNotBlank()) {
                    Spacer(Modifier.height(6.dp))
                    Text(ticket.datum.take(16), fontSize = 11.sp, color = textSec)
                }
            }
        }
    }
}
