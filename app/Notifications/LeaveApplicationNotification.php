<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApplicationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    protected $leaveApplication;
    protected $action;

    public function __construct(LeaveApplication $leaveApplication, $action)
    {
        $this->leaveApplication = $leaveApplication;
        $this->action = $action;
    }

     /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

public function toMail($notifiable)
{
    $mail = (new MailMessage);

    switch ($this->action) {
        case 'submitted':
                return $mail
                    ->subject('Leave Application Submitted')
                    ->greeting('Hello ' . $this->leaveApplication->employee->first_name . '!')
                    ->line('Your leave application has been submitted successfully.')
                    ->line('**Leave Type:** ' . $this->leaveApplication->leaveType->name)
                    ->line('**Duration:** ' . $this->leaveApplication->start_date->format('M d, Y') . ' to ' . $this->leaveApplication->end_date->format('M d, Y'))
                    ->line('**Total Days:** ' . $this->leaveApplication->total_days)
                    ->line('**Reason:** ' . $this->leaveApplication->reason)
                    ->action('View Application', url('/leaves/' . $this->leaveApplication->id))
                    ->line('Your manager will review your application shortly.');

        case 'approved':
                return $mail
                    ->subject('Leave Application Approved ✓')
                    ->greeting('Good news, ' . $this->leaveApplication->employee->first_name . '!')
                    ->line('Your leave application has been **approved**.')
                    ->line('**Leave Type:** ' . $this->leaveApplication->leaveType->name)
                    ->line('**Duration:** ' . $this->leaveApplication->start_date->format('M d, Y') . ' to ' . $this->leaveApplication->end_date->format('M d, Y'))
                    ->line('**Total Days:** ' . $this->leaveApplication->total_days)
                    ->line('**Approved By:** ' . $this->leaveApplication->approver->full_name)
                    ->line('**Notes:** ' . ($this->leaveApplication->approval_notes ?: 'No additional notes'))
                    ->action('View Application', url('/leaves/' . $this->leaveApplication->id))
                    ->line('Enjoy your time off!');

        case 'rejected':
            return $mail
                    ->subject('Leave Application Rejected')
                    ->greeting('Hello ' . $this->leaveApplication->employee->first_name . ',')
                    ->line('Unfortunately, your leave application has been rejected.')
                    ->line('**Leave Type:** ' . $this->leaveApplication->leaveType->name)
                    ->line('**Duration:** ' . $this->leaveApplication->start_date->format('M d, Y') . ' to ' . $this->leaveApplication->end_date->format('M d, Y'))
                    ->line('**Rejected By:** ' . $this->leaveApplication->approver->full_name)
                    ->line('**Reason:** ' . $this->leaveApplication->approval_notes)
                    ->action('View Application', url('/leaves/' . $this->leaveApplication->id))
                    ->line('Please contact your manager if you have any questions.');

        case 'cancelled':
            return $mail
                ->subject('Leave Application Cancelled')
                ->greeting('Hello ' . $this->leaveApplication->employee->first_name . ',')
                ->line('Your leave application has been cancelled.')
                ->line('**Leave Type:** ' . $this->leaveApplication->leaveType->name)
                ->line('**Duration:** ' . $this->leaveApplication->start_date->format('M d, Y') . ' to ' . $this->leaveApplication->end_date->format('M d, Y'))
                ->line('**Leave Balance Restored:** ' . $this->leaveApplication->total_days . ' days')
                ->action('View Application', url('/leaves/' . $this->leaveApplication->id))
                ->line('You can apply for a new leave if needed.');

        case 'new_application':
                return $mail
                    ->subject('New Leave Application Requires Approval')
                    ->greeting('Hello,')
                    ->line('A new leave application requires your approval.')
                    ->line('**Employee:** ' . $this->leaveApplication->employee->full_name)
                    ->line('**Leave Type:** ' . $this->leaveApplication->leaveType->name)
                    ->line('**Duration:** ' . $this->leaveApplication->start_date->format('M d, Y') . ' to ' . $this->leaveApplication->end_date->format('M d, Y'))
                    ->line('**Total Days:** ' . $this->leaveApplication->total_days)
                    ->line('**Reason:** ' . $this->leaveApplication->reason)
                    ->action('Review Application', url('/leaves/' . $this->leaveApplication->id))
                    ->line('Please review and approve/reject this application.');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'leave_application_id' => $this->leaveApplication->id,
            'action' => $this->action,
            'message' => $this->getNotificationMessage(),
        ];
    }

    protected function getNotificationMessage()
    {
        switch ($this->action) {
            case 'submitted':
                return 'Leave application submitted successfully';
            case 'approved':
                return 'Leave application approved';
            case 'rejected':
                return 'Leave application rejected';
            case 'new_application':
                return 'New leave application requires approval';
            default:
                return 'Leave application updated';
        }
    }
}
