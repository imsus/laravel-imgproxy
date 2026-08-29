# Laravel imgproxy

Terms for generating URLs that point at an imgproxy server and for persisting the processed images that server produces.

## Language

**Source**:
The original image, referenced by URL or by a Storage disk and path, that imgproxy fetches and processes.
_Avoid_: Original, input, raw image

**imgproxy URL**:
The signed, processed-image URL the builder derives from a source and a set of processing options. It is always recomputed on demand and never persisted.
_Avoid_: Generated url, image url

**Stored image**:
A processed image that has been fetched from imgproxy and written to a destination disk by the `toStorage` operation.
_Avoid_: Cached image, saved url, stored url

**Destination disk**:
The Storage disk a stored image is written to, and the path it occupies on that disk.
_Avoid_: Storage, target, bucket

## Public API

**Intent method**:
A high-level fluent method that expresses the desired image outcome in domain terms (`cover`, `fit`, `toWebp`, `storePublicly`), hiding imgproxy's wire format. It is the headline API.
_Avoid_: transformation, convenience method, sugar, helper

**Processing option**:
A single imgproxy image-processing setting (resize, width, quality, format, gravity), exposed one-to-one by a typed method and verbatim by `withOption()`.
_Avoid_: transformation, operation

**Option segment**:
The URL path piece that encodes a processing option, e.g. `rs:fill:300:300`, `q:80`, `bg:ffffff`.
_Avoid_: part, token
