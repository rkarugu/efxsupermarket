<?php

namespace App\Livewire;

use App\Models\ChequeBank;
use Livewire\Component;

class ChequeBankComponent extends Component
{

    public $name, $bank_code, $bounce_penalty;
    public $isOpen = 0;
    public $chequeBankId;

    protected $rules = [
        'name' => 'required',
        'bank_code' => 'required',
        'bounce_penalty' => 'required',
    ];

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        ChequeBank::updateOrCreate(['id' => $this->chequeBankId], [
            'bank' => $this->name,
            'bank_code' => $this->bank_code,
            'bounce_penalty' => $this->bounce_penalty,
        ]);
        $this->resetInputFields();
        $this->dispatch('message','Created Successful');
        $this->dispatch('close', '#exampleModal');
    }

    public function edit($id)
    {
        $chequeBank = ChequeBank::findOrFail($id);
        $this->chequeBankId = $id;
        $this->name = $chequeBank->bank;
        $this->bank_code = $chequeBank->bank_code;
        $this->bounce_penalty = $chequeBank->bounce_penalty;
    }

    public function delete($id)
    {
        ChequeBank::find($id)->delete();
        session()->flash('message', 'Cheque Bank Deleted Successfully.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->bank_code = '';
        $this->bounce_penalty = '';
    }
    public function render()
    {
        $chequeBanks = ChequeBank::all();
        return view('livewire.cheque-bank-component', ['chequeBanks' => $chequeBanks])->layout('layouts.admin.admin');
    }
}
