<?php

namespace App\Http\Livewire;

use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ContactList extends Component
{

    use WithFileUploads, WithPagination;

    public $openModal, $perPage = 25;
    public $excelFile, $excelData = [];

    public function upload()
    {
        $this->validate([
            'excelFile' => 'nullable|file|mimes:xlsx'
        ]);
        // dd($this->excelFile);
        if ($this->excelFile) {
            $path = Storage::put('documents', $this->excelFile);
            $url = storage_path("app/public/{$path}");
        } else {
            $url = public_path('docs/excel-format.xlsx');
        }

        $reader = IOFactory::createReader("Xlsx");
        $reader->setLoadAllSheets();
        $spreadsheet = $reader->load($url);
        $worksheet = $spreadsheet->getActiveSheet(); //Selecting The Active Sheet
        $highest_row = $worksheet->getHighestRow();
        $highest_col = "H";

        $highest_cell = $highest_col . $highest_row;
        $rang = "A2:" . $highest_cell; // Selecting The Cell Range

        $dataToArray = $spreadsheet->getActiveSheet()->rangeToArray(
            $rang, // The worksheet range that we want to retrieve
            NULL, // Value that should be returned for empty cells
            TRUE, // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            TRUE, // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            TRUE  // Should the array be indexed by cell row and cell column
        );
        $fields = ["e_tin", "tin_date", "name", "mobile", "address", "police_station", "old_tin", "circle_name"];
        $data = array_map(function ($row) use ($fields) {
            //Combining key value pair;
            return array_combine($fields, $row);
        }, $dataToArray);

        $this->excelData = array_map(function ($item) {
            if (trim($item["tin_date"]) != null) {
                $d = Carbon::createFromFormat("d/m/Y", $item["tin_date"]);
                $item["tin_date"] = $d->format("d-M-Y");
            }
            return $item;
        }, $data);

        $this->openModal = false;
    }

    public function confirmToImport()
    {
        if (count($this->excelData) > 0) {
            $contacts = array_map(function ($row) {
                $row['created_at'] = now();
                $row['updated_at'] = now();
                return $row;
            }, $this->excelData);
            Contact::insert($contacts);
            $this->excelData = [];
        }
    }


    public function getContactsProperty()
    {
        return Contact::query()->paginate($this->perPage);
    }
    public function render()
    {
        return view('livewire.contact-list')
            ->layoutData(['title' => 'Contact List']);
    }
}