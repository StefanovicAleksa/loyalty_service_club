<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\QrCodeService;

class GenerateQrCodeButton extends Component
{
    public $offerChoiceId;
    public $customerId;
    public string $class;
    public $errorMessage;
    public $isGenerating = false;

    public function mount($offerChoiceId, $customerId, $class = '')
    {
        $this->offerChoiceId = $offerChoiceId;
        $this->customerId = $customerId;
        $this->class = $class;
    }

    public function generateQrCode()
    {
        $this->isGenerating = true;
        $this->errorMessage = null;
        
        try {
            $qrCode = QrCodeService::createQrCode([
                'customer_id' => $this->customerId,
                'offer_choice_id' => $this->offerChoiceId
            ]);

            if ($qrCode) {
                $this->dispatch('qrCodeGenerated', qrCodeId: $qrCode->id);
                return redirect()->route('qrcode.show', ['id' => $qrCode->id]);
            } else {
                throw new \Exception('Failed to generate QR code.');
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to generate QR code. Please try again.';
            $this->dispatch('qrCodeGenerationFailed');
        } finally {
            $this->isGenerating = false;
        }
    }

    public function render()
    {
        return view('livewire.generate-qr-code-button');
    }
}