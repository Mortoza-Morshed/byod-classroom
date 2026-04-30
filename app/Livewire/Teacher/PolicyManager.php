<?php

namespace App\Livewire\Teacher;

use App\Models\ActivityLog;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PolicyManager extends Component
{
    public Classroom $classroom;

    public bool $showForm = false;

    public ?int $editingPolicyId = null;

    public string $name = '';

    public string $allowedUrlsInput = '';

    public string $blockedKeywordsInput = '';

    public bool $internetAccess = true;

    public bool $isDefault = false;

    public function mount(Classroom $classroom): void
    {
        abort_unless($classroom->teacher_id === Auth::id(), 403, 'You do not own this classroom.');

        $this->classroom = $classroom;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $policyId): void
    {
        $policy = $this->classroom->policies()->findOrFail($policyId);

        $this->editingPolicyId = $policy->id;
        $this->name = $policy->name;
        $this->allowedUrlsInput = implode(', ', $policy->allowed_urls ?? []);
        $this->blockedKeywordsInput = implode(', ', $policy->blocked_keywords ?? []);
        $this->internetAccess = $policy->internet_access;
        $this->isDefault = $policy->is_default;

        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->editingPolicyId = null;
        $this->name = '';
        $this->allowedUrlsInput = '';
        $this->blockedKeywordsInput = '';
        $this->internetAccess = true;
        $this->isDefault = false;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|min:2|max:80',
            'allowedUrlsInput' => 'nullable|string',
            'blockedKeywordsInput' => 'nullable|string',
            'internetAccess' => 'boolean',
            'isDefault' => 'boolean',
        ]);

        if ($this->isDefault) {
            $this->classroom->policies()->update(['is_default' => false]);
        }

        $data = [
            'name' => $this->name,
            'allowed_urls' => $this->parseInput($this->allowedUrlsInput),
            'blocked_keywords' => $this->parseInput($this->blockedKeywordsInput),
            'internet_access' => $this->internetAccess,
            'is_default' => $this->isDefault,
        ];

        if ($this->editingPolicyId) {
            $policy = $this->classroom->policies()->findOrFail($this->editingPolicyId);
            $policy->update($data);
            $action = 'policy.updated';
            $description = "Updated policy: {$policy->name}";
            $policyId = $policy->id;
        } else {
            $policy = $this->classroom->policies()->create($data);
            $action = 'policy.created';
            $description = "Created new policy: {$policy->name}";
            $policyId = $policy->id;
        }

        ActivityLog::record(
            action: $action,
            description: $description,
            userId: Auth::id(),
            metadata: ['policy_id' => $policyId]
        );

        $this->cancelForm();
    }

    public function delete(int $policyId): void
    {
        $policy = $this->classroom->policies()->findOrFail($policyId);
        $name = $policy->name;
        $policy->delete();

        ActivityLog::record(
            action: 'policy.deleted',
            description: "Deleted policy: {$name}",
            userId: Auth::id(),
            metadata: ['policy_id' => $policyId]
        );

        session()->flash('message', 'Policy deleted successfully.');
    }

    public function setDefault(int $policyId): void
    {
        $policy = $this->classroom->policies()->findOrFail($policyId);

        $this->classroom->policies()->update(['is_default' => false]);
        $policy->update(['is_default' => true]);

        ActivityLog::record(
            action: 'policy.set_default',
            description: "Set policy as default: {$policy->name}",
            userId: Auth::id(),
            metadata: ['policy_id' => $policy->id]
        );

        session()->flash('message', 'Default policy updated.');
    }

    private function parseInput(string $input): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', $input))
        ));
    }

    public function render()
    {
        return view('livewire.teacher.policy-manager', [
            'policies' => $this->classroom->policies()->latest()->get(),
        ]);
    }
}
