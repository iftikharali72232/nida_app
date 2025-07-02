<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Service;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    //
    public function create()
    {
        $services = Service::all();
        return view('articles.create', compact('services'));
    }

    public function edit(Article $article)
    {
        $services = Service::all();
        return view('articles.edit', compact('article', 'services'));
    }

    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    public function index()
    {
        $articles = Article::with('service')->paginate(10); // Paginate articles
        return view('articles.index', compact('articles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'text' => 'required|string',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $galleryImages = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $galleryImages[] = $imageName;
            }
        }

        Article::create([
            'title' => $validated['title'],
            'service_id' => $validated['service_id'],
            'text' => $validated['text'],
            'gallery_images' => json_encode($galleryImages),
        ]);

        return redirect()->route('articles.index')->with('success', 'Article created successfully!');
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'text' => 'required|string',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $galleryImages = (is_array($article->gallery_images) ? $article->gallery_images : json_decode($article->gallery_images, true) ?? []) ?? [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $galleryImages[] = $imageName;
            }
        }

        $article->update([
            'title' => $validated['title'],
            'service_id' => $validated['service_id'],
            'text' => $validated['text'],
            'gallery_images' => json_encode($galleryImages),
        ]);

        return redirect()->route('articles.index')->with('success', 'Article updated successfully!');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article deleted successfully!');
    }
    
    public function deleteImage(Request $request)
{
    $request->validate(['image' => 'required|string']);

    $imagePath = public_path('images/' . $request->image);

    // Delete the image file from storage
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Fetch the article using the provided ID
    $article = Article::where('id', $request->id)->first();

    // print_r($article); exit;

    if ($article) {
        // Ensure gallery_images is decoded properly
        $galleryImages = is_array($article->gallery_images) 
            ? $article->gallery_images 
            : json_decode($article->gallery_images, true) ?? [];

        // Remove the deleted image from the gallery_images array
        $updatedImages = array_filter($galleryImages, fn($image) => $image !== $request->image);

        // Save the updated gallery_images as JSON
        $article->gallery_images = json_encode(array_values($updatedImages)); // Reindex array keys
        $article->save();

        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Article not found'], 404);
}



}
