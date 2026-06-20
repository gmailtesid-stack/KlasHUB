package com.waveproject.kelashub

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class AssignmentAdapter(
    private var assignments: List<Assignment>,
    private val onLongClick: ((Assignment, View) -> Unit)? = null
) : RecyclerView.Adapter<AssignmentAdapter.ViewHolder>() {

    class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val tvSubject: TextView = view.findViewById(R.id.tvSubject)
        val tvTitle: TextView = view.findViewById(R.id.tvTitle)
        val tvDeadline: TextView = view.findViewById(R.id.tvDeadline)
        val tvType: TextView = view.findViewById(R.id.tvType)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_assignment, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val assignment = assignments[position]
        holder.tvSubject.text = assignment.subject_name.uppercase()
        holder.tvTitle.text = assignment.title
        holder.tvDeadline.text = "Deadline: ${assignment.deadline}"
        holder.tvType.text = assignment.type.uppercase()

        holder.itemView.setOnLongClickListener {
            onLongClick?.invoke(assignment, it)
            true
        }
    }

    override fun getItemCount() = assignments.size

    fun updateData(newAssignments: List<Assignment>) {
        assignments = newAssignments
        notifyDataSetChanged()
    }
}
