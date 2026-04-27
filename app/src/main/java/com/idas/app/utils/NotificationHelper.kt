package com.idas.app.utils

import android.app.AlarmManager
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.os.Build
import androidx.core.app.NotificationCompat
import com.idas.app.MainActivity
import java.text.SimpleDateFormat
import java.util.*

object NotificationHelper {

    const val CHANNEL_ID = "idas_reminders"
    const val CHANNEL_NAME = "Terminerinnerungen"

    fun createChannel(context: Context) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                CHANNEL_ID,
                CHANNEL_NAME,
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = "Erinnerungen für bevorstehende Arzttermine"
                enableVibration(true)
            }
            val manager = context.getSystemService(NotificationManager::class.java)
            manager.createNotificationChannel(channel)
        }
    }

    // Schedule a reminder 1 day before the appointment
    fun scheduleReminder(
        context: Context,
        terminId: Int,
        arztName: String,
        fachbereich: String,
        datumString: String  // format: "2026-05-15 09:00:00"
    ) {
        try {
            val sdf = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
            val appointmentDate = sdf.parse(datumString) ?: return
            val cal = Calendar.getInstance()
            cal.time = appointmentDate

            // Remind 1 day before at same time
            cal.add(Calendar.DAY_OF_MONTH, -1)
            val reminderTime = cal.timeInMillis

            // Only schedule if reminder is in the future
            if (reminderTime <= System.currentTimeMillis()) return

            val intent = Intent(context, ReminderReceiver::class.java).apply {
                putExtra("termin_id",   terminId)
                putExtra("arzt_name",   arztName)
                putExtra("fachbereich", fachbereich)
                putExtra("datum",       datumString)
            }

            val pendingIntent = PendingIntent.getBroadcast(
                context,
                terminId,
                intent,
                PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
            )

            val alarmManager = context.getSystemService(Context.ALARM_SERVICE) as AlarmManager

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.S) {
                if (alarmManager.canScheduleExactAlarms()) {
                    alarmManager.setExactAndAllowWhileIdle(
                        AlarmManager.RTC_WAKEUP, reminderTime, pendingIntent)
                }
            } else {
                alarmManager.setExactAndAllowWhileIdle(
                    AlarmManager.RTC_WAKEUP, reminderTime, pendingIntent)
            }
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    // Cancel a scheduled reminder
    fun cancelReminder(context: Context, terminId: Int) {
        val intent = Intent(context, ReminderReceiver::class.java)
        val pendingIntent = PendingIntent.getBroadcast(
            context, terminId, intent,
            PendingIntent.FLAG_NO_CREATE or PendingIntent.FLAG_IMMUTABLE
        )
        pendingIntent?.let {
            val alarmManager = context.getSystemService(Context.ALARM_SERVICE) as AlarmManager
            alarmManager.cancel(it)
        }
    }

    // Show notification immediately (for testing)
    fun showNotification(context: Context, terminId: Int, arztName: String,
                         fachbereich: String, datum: String) {
        val intent = Intent(context, MainActivity::class.java)
        val pendingIntent = PendingIntent.getActivity(
            context, 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        val notification = NotificationCompat.Builder(context, CHANNEL_ID)
            .setSmallIcon(android.R.drawable.ic_dialog_info)
            .setContentTitle("🗓 ${Strings.get("notif_reminder")}")
            .setContentText("${Strings.get("notif_tomorrow")}: $arztName ${Strings.get("notif_at")} ${datum.take(16)}")
            .setStyle(NotificationCompat.BigTextStyle()
                .bigText("${Strings.get("notif_tomorrow")}: $arztName\n$fachbereich\n${Strings.get("notif_at")} ${datum.take(16)}"))
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setContentIntent(pendingIntent)
            .setAutoCancel(true)
            .build()

        val manager = context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        manager.notify(terminId, notification)
    }
}

// Broadcast receiver that fires when alarm triggers
class ReminderReceiver : BroadcastReceiver() {
    override fun onReceive(context: Context, intent: Intent) {
        val terminId   = intent.getIntExtra("termin_id", 0)
        val arztName   = intent.getStringExtra("arzt_name") ?: ""
        val fachbereich = intent.getStringExtra("fachbereich") ?: ""
        val datum      = intent.getStringExtra("datum") ?: ""
        NotificationHelper.showNotification(context, terminId, arztName, fachbereich, datum)
    }
}
