<?php

namespace App\Livewire;

use App\Models\PromotionType;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\WithPagination;

class PromotionTypeComponent extends Component
{

    use WithPagination;

    public $name, $type_id;
    public $page_name = 'Promotion Types';
    public  $description;

    public function save()
    {

        $this->validate([
            'name'=>'required',
            'description'=>'required',
        ]);

        $type = PromotionType::updateOrCreate(
            ['id' =>  $this->type_id],
            [
                'name' =>$this->name,
                'description' =>$this->description,
            ]
        );

        $type->save();

        $this->resetInputs();
//        $this->emit('message','Action was Successful');
//        $this->emit('close','#showModal');
    }

    public function resetInputs()
    {
        $this->type_id = null;
        $this->name = null;
        $this->description = null;
    }

    public function edit($id)
    {
        $type =  PromotionType::find($id);
        $this->type_id = $id;
        $this->name = $type->name;
        $this->description = $type->description;

    }

    public function setActive($id)
    {
        $this->type_id = $id;
    }

    public function delete()
    {
        PromotionType::destroy($this->type_id);
        $this->emit('message','Delete Successful');
        $this->emit('close', '#deleteRecordModal');
    }
    public function render()
    {

        return view('livewire.promotion-type-component',[
            'items' => PromotionType::latest()->paginate(100)
        ]);
    }
}
