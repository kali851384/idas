package com.idas.app

import android.Manifest
import android.content.pm.PackageManager
import android.os.Build
import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.animation.*
import androidx.compose.runtime.*
import androidx.core.content.ContextCompat
import androidx.navigation.compose.rememberNavController
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import com.idas.app.models.Arzt
import com.idas.app.models.BookingConfirmData
import com.idas.app.screens.*
import com.idas.app.ui.theme.IDASTheme
import com.idas.app.utils.NotificationHelper
import com.idas.app.utils.PrefsHelper
import com.idas.app.utils.Strings

class MainActivity : ComponentActivity() {

    private val requestPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        NotificationHelper.createChannel(this)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS)
                != PackageManager.PERMISSION_GRANTED) {
                requestPermissionLauncher.launch(Manifest.permission.POST_NOTIFICATIONS)
            }
        }
        setContent {
            val darkModeState = remember { mutableStateOf(PrefsHelper.isDarkMode(this)) }
            IDASTheme(darkTheme = darkModeState.value) { IDASApp(activity = this, darkModeState = darkModeState) }
        }
    }
}

@Composable
fun IDASApp(activity: MainActivity, darkModeState: androidx.compose.runtime.MutableState<Boolean>) {
    val navController  = rememberNavController()
    var token          by remember { mutableStateOf("") }
    var patientId      by remember { mutableStateOf(0) }
    var patientName    by remember { mutableStateOf("") }
    var selectedArzt   by remember { mutableStateOf<Arzt?>(null) }
    var selectedFach   by remember { mutableStateOf("") }
    var currentLang    by remember { mutableStateOf(Strings.language) }
    var darkMode       by darkModeState
    var lastTermin     by remember { mutableStateOf<BookingConfirmData?>(null) }

    val startDest = remember {
        val ctx = activity
        when {
            PrefsHelper.isStayLoggedIn(ctx) && PrefsHelper.getSavedToken(ctx).isNotBlank() -> {
                token       = PrefsHelper.getSavedToken(ctx)
                patientId   = PrefsHelper.getSavedPatientId(ctx)
                patientName = PrefsHelper.getSavedName(ctx)
                "dashboard"
            }
            !PrefsHelper.hasSeenOnboarding(ctx) -> "splash"
            else -> "splash"
        }
    }

    // No animation for any navigation
    val noEnter = EnterTransition.None
    val noExit  = ExitTransition.None

    NavHost(
        navController = navController,
        startDestination = startDest,
        enterTransition = { noEnter },
        exitTransition  = { noExit },
        popEnterTransition  = { noEnter },
        popExitTransition   = { noExit }
    ) {

        composable("splash") {
            SplashScreen(onFinished = {
                val ctx = activity
                when {
                    PrefsHelper.isStayLoggedIn(ctx) && PrefsHelper.getSavedToken(ctx).isNotBlank() -> {
                        token       = PrefsHelper.getSavedToken(ctx)
                        patientId   = PrefsHelper.getSavedPatientId(ctx)
                        patientName = PrefsHelper.getSavedName(ctx)
                        navController.navigate("dashboard") { popUpTo("splash") { inclusive = true } }
                    }
                    !PrefsHelper.hasSeenOnboarding(ctx) ->
                        navController.navigate("onboarding") { popUpTo("splash") { inclusive = true } }
                    else ->
                        navController.navigate("login") { popUpTo("splash") { inclusive = true } }
                }
            })
        }

        composable("onboarding") {
            OnboardingScreen(onFinish = {
                PrefsHelper.setOnboardingSeen(activity)
                navController.navigate("login") { popUpTo("onboarding") { inclusive = true } }
            })
        }

        composable("login") {
            LoginScreen(
                onLoginSuccess = { t, pid, name ->
                    token = t; patientId = pid; patientName = name
                    navController.navigate("dashboard") { popUpTo("login") { inclusive = true } }
                },
                onGoToRegister = { navController.navigate("register") }
            )
        }

        composable("register") {
            RegisterScreen(
                onRegisterSuccess = { navController.navigate("login") },
                onBack = { navController.popBackStack() }
            )
        }

        composable("dashboard") {
            DashboardScreen(
                token       = token,
                patientName = patientName,
                currentLang = currentLang,
                darkMode    = darkMode,
                onDarkModeToggle = { enabled ->
                    darkMode = enabled
                    PrefsHelper.setDarkMode(activity, enabled)
                },
                onSymptome  = { navController.navigate("symptome") },
                onTermine   = { navController.navigate("termine") },
                onProfil    = { navController.navigate("profil") },
                onSupport   = { navController.navigate("support") },
                onLanguage  = { lang -> Strings.language = lang; currentLang = lang },
                onLogout    = {
                    PrefsHelper.clearLogin(activity)
                    token = ""; patientId = 0; patientName = ""
                    navController.navigate("login") { popUpTo("dashboard") { inclusive = true } }
                }
            )
        }

        composable("symptome") {
            SymptomScreen(token = token,
                onBack = { navController.popBackStack() },
                onResults = { ids -> navController.navigate("matching/$ids") })
        }

        composable("matching/{symptomIds}") { back ->
            val ids = back.arguments?.getString("symptomIds") ?: ""
            MatchingScreen(
                token = token, symptomIds = ids,
                onBack = { navController.popBackStack() },
                onBook = { arztId, arztName, fachbereich ->
                    navController.navigate("buchen/$arztId/$arztName/$fachbereich/$ids")
                },
                onDoctorDetail = { arzt, fach ->
                    selectedArzt = arzt; selectedFach = fach
                    navController.navigate("doctor_detail")
                }
            )
        }

        composable("doctor_detail") {
            val arzt = selectedArzt
            if (arzt == null) {
                navController.popBackStack()
            } else {
                DoctorDetailScreen(
                    arzt = arzt, fachbereich = selectedFach,
                    onBack = { navController.popBackStack() },
                    onBook = { arztId, arztName, fachbereich ->
                        navController.navigate("buchen/$arztId/$arztName/$fachbereich/")
                    }
                )
            }
        }

        composable("buchen/{arztId}/{arztName}/{fachbereich}/{symptomIds}") { back ->
            val arztId      = back.arguments?.getString("arztId")?.toIntOrNull() ?: 0
            val arztName    = back.arguments?.getString("arztName") ?: ""
            val fachbereich = back.arguments?.getString("fachbereich") ?: ""
            val sIds        = back.arguments?.getString("symptomIds") ?: ""
            BookingScreen(
                token = token, patientId = patientId, patientName = patientName,
                arztId = arztId, arztName = arztName, fachbereich = fachbereich,
                symptomIds = sIds, onBack = { navController.popBackStack() },
                onBooked = { termin ->
                    lastTermin = termin
                    NotificationHelper.scheduleReminder(activity, termin.terminId,
                        termin.arztName, termin.fachbereich, termin.datum)
                    navController.currentBackStackEntry?.savedStateHandle?.set("termin", termin)
                    navController.navigate("bestaetigung")
                }
            )
        }

        composable("bestaetigung") {
            val termin = navController.previousBackStackEntry
                ?.savedStateHandle?.get<BookingConfirmData>("termin") ?: lastTermin
            if (termin == null) {
                navController.popBackStack()
            } else {
                BestaetigungScreen(
                    termin = termin,
                    onHome = { navController.navigate("dashboard") { popUpTo("dashboard") { inclusive = false } } },
                    onTermine = { navController.navigate("termine") },
                    onQRCode = { lastTermin = termin; navController.navigate("qrcode") }
                )
            }
        }

        composable("qrcode") {
            QRScreen(termin = lastTermin, onBack = { navController.popBackStack() })
        }

        composable("termine") {
            TermineScreen(token = token, patientName = patientName, activity = activity,
                onBack = { navController.popBackStack() })
        }

        composable("profil") {
            ProfilScreen(
                token    = token,
                darkMode = darkMode,
                onDarkModeToggle = { enabled ->
                    darkMode = enabled
                    PrefsHelper.setDarkMode(activity, enabled)
                },
                onBack = { navController.popBackStack() }
            )
        }

        composable("support") {
            SupportScreen(token = token, onBack = { navController.popBackStack() })
        }
    }
}