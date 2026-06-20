package com.waveproject.kelashub

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class ScheduleAdapter(
    private var list: List<ScheduleData>,
    private val onLongClick: ((ScheduleData, View) -> Unit)? = null
) : RecyclerView.Adapter<ScheduleAdapter.VH>() {
    class VH(v: View) : RecyclerView.ViewHolder(v) {
        val tvSubject: TextView = v.findViewById(R.id.tvSubject)
        val tvLecturer: TextView = v.findViewById(R.id.tvLecturer)
        val tvDay: TextView = v.findViewById(R.id.tvDay)
        val tvTime: TextView = v.findViewById(R.id.tvTime)
        val tvRoom: TextView = v.findViewById(R.id.tvRoom)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): VH {
        return VH(LayoutInflater.from(parent.context).inflate(R.layout.item_schedule, parent, false))
    }

    override fun onBindViewHolder(holder: VH, position: Int) {
        val item = list[position]
        holder.tvSubject.text = item.subjectName
        holder.tvLecturer.text = item.lecturer
        holder.tvDay.text = item.day

        val start = item.timeStart?.take(5) ?: "00:00"
        val end = item.timeEnd?.take(5) ?: "00:00"
        holder.tvTime.text = "$start - $end"
        
        
        holder.tvRoom.text = "Ruang ${item.room ?: "-"}"

        holder.itemView.setOnLongClickListener {
            onLongClick?.invoke(item, it)
            true
        }
    }

    override fun getItemCount() = list.size

    fun updateData(newData: List<ScheduleData>) {
        list = newData
        notifyDataSetChanged()
    }
}
