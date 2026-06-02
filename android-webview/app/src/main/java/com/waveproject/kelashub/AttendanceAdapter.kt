package com.waveproject.kelashub

import android.graphics.Color
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class AttendanceAdapter(private var list: List<AttendanceStat>) : RecyclerView.Adapter<AttendanceAdapter.VH>() {
    class VH(v: View) : RecyclerView.ViewHolder(v) {
        val tvSubject: TextView = v.findViewById(R.id.tvSubject)
        val tvStatus: TextView = v.findViewById(R.id.tvStatus)
        val tvNyawa: TextView = v.findViewById(R.id.tvNyawa)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): VH {
        return VH(LayoutInflater.from(parent.context).inflate(R.layout.item_attendance, parent, false))
    }

    override fun onBindViewHolder(holder: VH, position: Int) {
        val item = list[position]
        holder.tvSubject.text = item.subject
        holder.tvNyawa.text = item.nyawa.toString()
        holder.tvStatus.text = item.status

        if (item.nyawa <= 1) {
            holder.tvNyawa.setTextColor(Color.parseColor("#ef4444")) // danger
            holder.tvStatus.setTextColor(Color.parseColor("#ef4444"))
        } else {
            holder.tvNyawa.setTextColor(Color.parseColor("#10b981")) // success
            holder.tvStatus.setTextColor(Color.parseColor("#10b981"))
        }
    }

    override fun getItemCount() = list.size

    fun updateData(newData: List<AttendanceStat>) {
        list = newData
        notifyDataSetChanged()
    }
}
