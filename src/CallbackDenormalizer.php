<?php

namespace Gander\Symfony\Component\Serializer\Normalizer;

use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class CallbackDenormalizer
 *
 * @package Gander\Symfony\Component\Serializer\Normalizer
 */
class CallbackDenormalizer implements DenormalizerInterface, SerializerAwareInterface {
	/**
	 * @var SerializerInterface|DenormalizerInterface
	 */
	private $serializer;

	/**
	 * @var callable
	 */
	private $denormalizeCallback;

	/**
	 * @var callable
	 */
	private $supportsCallback;

	/**
	 * CallbackDenormalizer constructor.
	 * @param callable $denormalize
	 * @param callable $supports
	 */
	public function __construct(callable $supports, callable $denormalize) {
		$this->denormalizeCallback = $denormalize;
		$this->supportsCallback    = $supports;
	}

	/**
	 * @param mixed       $data
	 * @param string      $class
	 * @param string|null $format
	 * @param array       $context
	 * @return object
	 */
	public function denormalize($data, $class, $format = null, array $context = array()) {
		if ($this->serializer === null) {
			throw new BadMethodCallException('Please set a serializer before calling denormalize()!');
		}

		return $this->serializer->denormalize(call_user_func($this->denormalizeCallback, $data, $class, $format, $context), $class, $format, $context);
	}

	/**
	 * {@inheritdoc}
	 */
	public function supportsDenormalization($data, $type, $format = null, array $context = array()) {
		return call_user_func($this->supportsCallback, $data, $type, $context);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setSerializer(SerializerInterface $serializer) {
		if (!$serializer instanceof DenormalizerInterface) {
			throw new InvalidArgumentException('Expected a serializer that also implements DenormalizerInterface.');
		}

		$this->serializer = $serializer;
	}
}
