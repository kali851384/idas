package com.idas.app.utils

import android.content.Context

object PrefsHelper {
    private const val PREFS = "idas_prefs"
    private const val KEY_ONBOARDED  = "has_seen_onboarding"
    private const val KEY_TOKEN      = "saved_token"
    private const val KEY_PATIENT_ID = "saved_patient_id"
    private const val KEY_NAME       = "saved_name"
    private const val KEY_STAY_IN    = "stay_logged_in"
    private const val KEY_DARK_MODE  = "dark_mode"

    fun hasSeenOnboarding(ctx: Context): Boolean =
        ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
            .getBoolean(KEY_ONBOARDED, false)

    fun setOnboardingSeen(ctx: Context) =
        ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE).edit()
            .putBoolean(KEY_ONBOARDED, true).apply()

    fun saveLogin(ctx: Context, token: String, patientId: Int, name: String) =
        ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE).edit()
            .putString(KEY_TOKEN, token)
            .putInt(KEY_PATIENT_ID, patientId)
            .putString(KEY_NAME, name)
            .putBoolean(KEY_STAY_IN, true)
            .apply()

    fun clearLogin(ctx: Context) =
        ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE).edit()
            .remove(KEY_TOKEN).remove(KEY_PATIENT_ID).remove(KEY_NAME)
            .putBoolean(KEY_STAY_IN, false).apply()

    fun getSavedToken(ctx: Context): String =
        ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
            .getString(KEY_TOKEN, "") ?: ""

    fun getSavedPatientId(ctx: Context): Int =
        ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
            .getInt(KEY_PATIENT_ID, 0)

    fun getSavedName(ctx: Context): String =
        ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
            .getString(KEY_NAME, "") ?: ""

    fun isStayLoggedIn(ctx: Context): Boolean =
        ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
            .getBoolean(KEY_STAY_IN, false)

    fun isDarkMode(ctx: Context): Boolean =
        ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
            .getBoolean(KEY_DARK_MODE, false)

    fun setDarkMode(ctx: Context, enabled: Boolean) =
        ctx.getSharedPreferences(PREFS, Context.MODE_PRIVATE).edit()
            .putBoolean(KEY_DARK_MODE, enabled).apply()
}
