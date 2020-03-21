<?php

require_once("html_core.php");
require_once("config.php");
require_once("html_layout.php");

class HtmlDocument extends HtmlElement
{
  private $Title = "HTMLToPHP Framework";
  private $Language = "en";
  private $Charset = "UFT-8";
  private $Body;
  private $HeaderQueue = array();

  public function __construct() {
    parent::InitializeHtml();
  }

  public function SetLanguage(string $string) {
    $this->Language = $string;
    return $this;
  }

  public function SetTitle(string $string) {
    $this->Title = $string;
    return $this;
  }

  public function SetCharset(string $string) {
    $this->Title = $string;
    return $this;
  }

  public function SetBody(Html $item) {
    $this->Body = $item;
    return $this;
  }

  public function AddHeader(HtmlDocumentHeader $item) {
    array_push($this->HeaderQueue, $item);
    return $this;
  }

  public function GenerateHeaderQueue() : HtmlQueue {
    $argq = $this->HeaderQueue;
    array_push($argq, (new HtmlDocumentLink)
      ->SetRelItem("stylesheet")
      ->SetTypeItem("text/css")
      ->SetHrefItem(HTML_BASE_CSS_PATH)
    );
    array_push($argq, (new HtmlDocumentMeta)->AddArgument(
      (new HtmlArgument)
      ->SetName("charset")
      ->AddItem($this->Charset)
    ));
    array_push($argq, new HtmlTag("title", array(), $this->Title));
    $queue = new HtmlQueue;
    foreach ($argq as $item) {
      $queue->AddItem($item);
    }
    return $queue;
  }

  public function Generate() : string {
    return "<!DOCTYPE html>".(new HtmlTag(
      "html", 
      array(
        (new HtmlArgument)
        ->SetName("lang")
        ->AddItem($this->Language)
      ),
      (new HtmlQueue)
      ->AddItem(
        new HtmlTag(
          "head",
          array(),
          $this->GenerateHeaderQueue()->GetReverseQueue()->Generate()
        )
      )
      ->AddItem(
        new HtmlTag(
          "body",
          $this->GetArgumentsQueue(),
          $this->Body->Generate()
        )
      )->Generate()
    ))->Generate();
  }
}

abstract class HtmlDocumentHeader extends HtmlElement
{
  public function IsEmpty() : bool {
    return false;
  }
}

class HtmlDocumentMeta extends HtmlDocumentHeader
{
  private $PropertyArgument;
  private $NameArgument;
  private $TypeArgument;
  private $ContentArgument;
  
  public function __construct() {
    parent::InitializeHtml();
    $this->PropertyArgument = (new HtmlArgument)
    ->SetName("property");
    $this->NameArgument = (new HtmlArgument)
    ->SetName("name");
    $this->TypeArgument = (new HtmlArgument)
    ->SetName("type");
    $this->ContentArgument = (new HtmlArgument)
    ->SetName("content");
  }

  public function SetPropertyItem(string $string) {
    $this->PropertyArgument->RemoveAllItems();
    $this->PropertyArgument->AddItem($string);
    return $this;
  }

  public function SetTypeItem(string $string) {
    $this->TypeArgument->RemoveAllItems();
    $this->TypeArgument->AddItem($string);
    return $this;
  }

  public function SetContentItem(string $string) {
    $this->ContentArgument->RemoveAllItems();
    $this->ContentArgument->AddItem($string);
    return $this;
  }

  public function SetNameItem(string $string) {
    $this->NameArgument->RemoveAllItems();
    $this->NameArgument->AddItem($string);
    return $this;
  }

  public function &GetPropertyArgumentLink() : HtmlArgument {
    return $this->PropertyArgument;
  }

  public function &GetNameArgumentLink() : HtmlArgument {
    return $this->NameArgument;
  }

  public function &GetTypeArgumentLink() : HtmlArgument {
    return $this->TypeArgument;
  }

  public function &GetContentArgumentLink() : HtmlArgument {
    return $this->ContentArgument;
  }

  public function Generate() : string {
    $argq = $this->GetArgumentsQueue();
    array_push($argq, $this->NameArgument);
    array_push($argq, $this->TypeArgument);
    array_push($argq, $this->PropertyArgument);
    array_push($argq, $this->ContentArgument);
    $argq = array_splice($argq, 3, count($argq) - 1, array());
    return (new HtmlTag(
      "meta", 
      array_reverse($argq), 
      null
    ))->Generate();
  }
}

class HtmlDocumentLink extends HtmlDocumentHeader
{
  private $RelArgument;
  private $HrefArgument;
  private $TypeArgument;
  private $SizesArgument;
  
  public function __construct() {
    parent::InitializeHtml();
    $this->RelArgument = (new HtmlArgument)
    ->SetName("rel");
    $this->HrefArgument = (new HtmlArgument)
    ->SetName("href");
    $this->TypeArgument = (new HtmlArgument)
    ->SetName("type");
    $this->SizesArgument = (new HtmlArgument)
    ->SetName("sizes");
  }

  public function SetRelItem(string $string) {
    $this->RelArgument->RemoveAllItems();
    $this->RelArgument->AddItem($string);
    return $this;
  }

  public function SetTypeItem(string $string) {
    $this->TypeArgument->RemoveAllItems();
    $this->TypeArgument->AddItem($string);
    return $this;
  }

  public function SetHrefItem(string $string) {
    $this->HrefArgument->RemoveAllItems();
    $this->HrefArgument->AddItem($string);
    return $this;
  }

  public function SetSizesItem(string $string) {
    $this->SizesArgument->RemoveAllItems();
    $this->SizesArgument->AddItem($string);
    return $this;
  }

  public function &GetRelArgumentLink() : HtmlArgument {
    return $this->RelArgument;
  }

  public function &GetHrefArgumentLink() : HtmlArgument {
    return $this->HrefArgument;
  }

  public function &GetTypeArgumentLink() : HtmlArgument {
    return $this->TypeArgument;
  }

  public function &GetSizesArgumentLink() : HtmlArgument {
    return $this->SizesArgument;
  }

  public function Generate() : string {
    $argq = $this->GetArgumentsQueue();
    array_push($argq, $this->RelArgument);
    array_push($argq, $this->SizesArgument);
    array_push($argq, $this->TypeArgument);
    array_push($argq, $this->HrefArgument);
    $argq = array_splice($argq, 3, count($argq) - 1, array());
    return (new HtmlTag(
      "link", 
      $argq, 
      null
    ))->Generate();
  }
}

class HtmlDocumentScript extends HtmlDocumentHeader
{
  private $SrcArgument;
  private $Content;
  
  public function __construct() {
    parent::InitializeHtml();
    $this->SrcArgument = (new HtmlArgument)
    ->SetName("src");
  }

  public function SetSrcItem(string $string) {
    $this->SrcArgument->RemoveAllItems();
    $this->SrcArgument->AddItem($string);
    return $this;
  }

  public function SetContent($other) {
    $this->Content = $other;
    return $this;
  }

  public function &GetSizesArgumentLink() : HtmlArgument {
    return $this->SrcArgument;
  }

  public function Generate() : string {
    $argq = $this->GetArgumentsQueue();
    array_push($argq, $this->SrcArgument);
    $argq = array_splice($argq, 3, count($argq) - 1, array());
    return (new HtmlTag("script", $argq, $this->Content))->Generate();
  }
}

?>