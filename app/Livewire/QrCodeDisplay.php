<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\QrCode;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Log;

class QrCodeDisplay extends Component
{
    public $qrCodeId;
    public $qrCodeImage;
    public $isValid;
    public $isLoading = true;
    public $pollCount = 0;
    public $maxPollCount = 60; // 5 minutes of polling at 5-second intervals

    public function mount(int $id)
    {
        $this->qrCodeId = $id;
        $this->loadQrCode();
    }

    public function loadQrCode()
    {
        try {
            $qrCode = QrCode::findOrFail($this->qrCodeId);
            $this->isValid = QrCodeService::checkQrCodeValidity($this->qrCodeId);
            $this->qrCodeImage = $this->isValid ? QrCodeService::generateQrCodeImage($qrCode->id) : null;
            
            Log::info('QR Code loaded', ['id' => $this->qrCodeId, 'isValid' => $this->isValid]);

            if ($this->pollCount >= $this->maxPollCount) {
                $this->isValid = false;
                Log::info('Max poll count reached', ['id' => $this->qrCodeId]);
            }
        } catch (\Exception $e) {
            Log::error('Error loading QR Code', ['id' => $this->qrCodeId, 'error' => $e->getMessage()]);
            $this->isValid = false;
        } finally {
            $this->isLoading = false;
        }
    }

    public function checkRedemption()
    {
        return ['isRedeemed' => QrCodeService::isRedeemed($this->qrCodeId)];
    }

    public function render()
    {
        return view('livewire.qr-code-display');
    }
}