package com.waveproject.kelashub

import android.os.Bundle
import android.view.Gravity
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.FrameLayout
import android.widget.TextView
import androidx.fragment.app.Fragment

class PlaceholderFragment(private val text: String) : Fragment() {
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val tv = TextView(requireContext()).apply {
            text = this@PlaceholderFragment.text
            setTextColor(resources.getColor(android.R.color.white, null))
            gravity = Gravity.CENTER
            textSize = 20f
        }
        val layout = FrameLayout(requireContext()).apply {
            addView(tv, FrameLayout.LayoutParams(
                FrameLayout.LayoutParams.MATCH_PARENT,
                FrameLayout.LayoutParams.MATCH_PARENT
            ))
        }
        return layout
    }
}
