<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;
use Models\App\Recipe as AppRecipe;

class RecipeController extends BaseController
{

    public function all(){
        // return Recipe::all();
        Log::info("authentication was successful");
        return Auth::user();
    }

    public function get($id){
        if ( Auth::user()){
            $recipe = Recipe::find($id);
            $recipe->ingredients = Ingredient::where('recipe', $recipe->id)->get();
            return $recipe;
        }   
    }

    public function create(Request $request){
        if(Auth::user()){
            Log::info($request);
            $recipe = new Recipe();
            $recipe->title = $request->input('title');
            $recipe->description = $request->input('description');
            $recipe->duration_in_minutes = intval($request->input('duration'));
            $recipe->save();
            $ingredients = $request->input('ingredients');
            foreach ($ingredients as $ingredient){
                $ingredientInstance = new Ingredient();
                $ingredientInstance->name = $ingredient['ingredient'];
                $ingredientInstance->recipe = $recipe->id;
                $ingredientInstance->amount = $ingredient['amount'];
                $ingredientInstance->unit = $ingredient['unit'];
                $ingredientInstance->save();
            }
        }
    }
}
