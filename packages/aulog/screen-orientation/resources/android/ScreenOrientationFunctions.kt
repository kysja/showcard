package com.aulog.plugins.screen_orientation

import android.content.pm.ActivityInfo
import androidx.fragment.app.FragmentActivity
import com.nativephp.mobile.bridge.BridgeFunction
import com.nativephp.mobile.bridge.BridgeResponse

object ScreenOrientationFunctions {

    // Sensor landscape lets the phone be held either way round.
    class LockLandscape(private val activity: FragmentActivity) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            activity.runOnUiThread {
                activity.requestedOrientation = ActivityInfo.SCREEN_ORIENTATION_SENSOR_LANDSCAPE
            }

            return BridgeResponse.success(mapOf("orientation" to "landscape"))
        }
    }

    class LockPortrait(private val activity: FragmentActivity) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            activity.runOnUiThread {
                activity.requestedOrientation = ActivityInfo.SCREEN_ORIENTATION_PORTRAIT
            }

            return BridgeResponse.success(mapOf("orientation" to "portrait"))
        }
    }
}
