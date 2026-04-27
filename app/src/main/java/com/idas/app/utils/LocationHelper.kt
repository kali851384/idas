package com.idas.app.utils

import android.Manifest
import android.content.Context
import android.content.pm.PackageManager
import android.location.Location
import android.location.LocationManager
import androidx.core.content.ContextCompat

object LocationHelper {

    fun getLastKnownLocation(context: Context): Location? {
        if (ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION)
            != PackageManager.PERMISSION_GRANTED &&
            ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_COARSE_LOCATION)
            != PackageManager.PERMISSION_GRANTED) {
            return null
        }
        val lm = context.getSystemService(Context.LOCATION_SERVICE) as LocationManager
        // Try all providers including passive
        val providers = listOf(
            LocationManager.GPS_PROVIDER,
            LocationManager.NETWORK_PROVIDER,
            LocationManager.PASSIVE_PROVIDER
        )
        return providers
            .filter { runCatching { lm.isProviderEnabled(it) }.getOrDefault(false) }
            .mapNotNull { runCatching { lm.getLastKnownLocation(it) }.getOrNull() }
            .maxByOrNull { it.time }
    }

    // Fallback: get fresh location using a callback (for when lastKnown is null)
    fun requestFreshLocation(context: Context, onResult: (Location?) -> Unit) {
        if (ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_FINE_LOCATION)
            != PackageManager.PERMISSION_GRANTED &&
            ContextCompat.checkSelfPermission(context, Manifest.permission.ACCESS_COARSE_LOCATION)
            != PackageManager.PERMISSION_GRANTED) {
            onResult(null); return
        }
        val lm = context.getSystemService(Context.LOCATION_SERVICE) as LocationManager
        val listener = object : android.location.LocationListener {
            override fun onLocationChanged(location: Location) {
                lm.removeUpdates(this)
                onResult(location)
            }
            @Deprecated("Deprecated in Java")
            override fun onStatusChanged(provider: String?, status: Int, extras: android.os.Bundle?) {}
        }
        try {
            val provider = when {
                lm.isProviderEnabled(LocationManager.NETWORK_PROVIDER) -> LocationManager.NETWORK_PROVIDER
                lm.isProviderEnabled(LocationManager.GPS_PROVIDER)     -> LocationManager.GPS_PROVIDER
                else -> { onResult(null); return }
            }
            lm.requestLocationUpdates(provider, 0L, 0f, listener)
            // Timeout after 5 seconds
            android.os.Handler(android.os.Looper.getMainLooper()).postDelayed({
                lm.removeUpdates(listener)
                onResult(null)
            }, 5000)
        } catch (e: Exception) { onResult(null) }
    }

    // Parse address string to rough coordinates (Hannover area fallback)
    // In a real app you'd use Geocoding API
    fun distanceKm(lat1: Double, lon1: Double, lat2: Double, lon2: Double): Double {
        val r = 6371.0
        val dLat = Math.toRadians(lat2 - lat1)
        val dLon = Math.toRadians(lon2 - lon1)
        val a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(Math.toRadians(lat1)) * Math.cos(Math.toRadians(lat2)) *
                Math.sin(dLon/2) * Math.sin(dLon/2)
        return r * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))
    }
}
