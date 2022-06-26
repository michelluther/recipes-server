<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;
use Models\App\Recipe as AppRecipe;
use Illuminate\Support\Facades\Storage;

class RecipeController extends BaseController
{

    private function convertAmount($amount)
    {
        $convertedAmount = iconv('UTF-8', 'ASCII//TRANSLIT', $amount);
        $convertedAmount = str_replace(',', '.', $convertedAmount);
        Log::info('this is the amount: ' .  $convertedAmount);
        $exploded = explode('/', $convertedAmount);
        if ($convertedAmount && count($exploded) > 1) {
            Log::info("found something for amount " . $convertedAmount);

            $calculatedFraction = intval($exploded[0]) / intval($exploded[1]);
            Log::info("this is what I calculated" . $calculatedFraction);
            return $calculatedFraction;
        } else {
            Log::info('it is not that complicated');
            return $convertedAmount;
        }
    }

    public function all()
    {
        if (Auth::user()) {
            Log::info("authentication was successful");
            $recipes = Recipe::all();
            return $recipes;
        }
    }

    public function get($id)
    {
        if (Auth::user()) {
            $recipe = Recipe::find($id);
            $recipe->ingredients = Ingredient::where('recipe', $recipe->id)->get();
            // return response()->json($recipe);
            return $recipe;
        }
    }

    public function delete($id)
    {
        if (Auth::user()) {
            $recipe = Recipe::find($id);
            $recipe->ingredients = Ingredient::where('recipe', $recipe->id)->delete();
            // return response()->json($recipe);
            $recipe->delete();
        }
    }

    public function create(Request $request)
    {
        Log::info('i am in create');

        if (Auth::user()) {
            Log::info('look, an authorized upload');
            if ($request->header('Content-Type') == 'application/json') {
                $this->createFromJson($request);
            } else {
                $this->createFromImage($request);
            }
        }
    }

    private function createFromImage(Request $request)
    {
        $title = $request->input('title');

        Log::info('look, an image upload for recipe: ' . $title);
        $recipe = new Recipe();
        $recipe->title = $title;
        $recipe->save();
        foreach ($request->files as $fileInRequest) {
            $file = $request->file($fileInRequest);
            Log::info($fileInRequest->getClientOriginalName());
            // $fileInRequest->move('recipeImages', $recipe->id . '_image.jpg');
            Storage::putFileAs(
                'recipeImages/',
                $fileInRequest,
                $recipe->id . '_image.jpg'
            );
        }
    }

    private function createFromJson(Request $request)
    {

        Log::info($request);
        $recipe = new Recipe();
        $recipe->title = $request->input('title');
        $recipe->description = $request->input('description');
        $imageUrl = $request->input('imageUrl');
        Log::info(isset($imageUrl) == 1);
        $recipe->duration_in_minutes = intval($request->input('duration'));
        $recipe->save();
        // if (isset($imageUrl)){
            $recipeImage = file_get_contents($request->input('imageUrl'));
            Log::info($recipeImage);
            Log::info('I will upload an image');
            Storage::put('recipeImages/' . $recipe->id . '_image.jpg', $recipeImage);
            Log::info('I have uploaded an image');
        // }
        $ingredients = $request->input('ingredients');
        foreach ($ingredients as $ingredient) {
            $ingredientInstance = new Ingredient();
            $ingredientInstance->name = $ingredient['ingredient'];
            $ingredientInstance->recipe = $recipe->id;
            if ($ingredient['amount'] != '') {
                $ingredientInstance->amount = $this->convertAmount($ingredient['amount']);
            }
            $ingredientInstance->unit = $ingredient['unit'];
            $ingredientInstance->save();
        }
    }
}
